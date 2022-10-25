<?php

declare(strict_types=1);

namespace Imi\Meter\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Meter\Annotation\Timed;
use Imi\Meter\Contract\ITimerSample;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Text;
use Imi\Worker;

/**
 * @Aspect
 */
class TimedAspect
{
    /**
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Timed::class
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function around(AroundJoinPoint $joinPoint)
    {
        $class = BeanFactory::getObjectClass($joinPoint->getTarget());
        $method = $joinPoint->getMethod();
        /** @var Timed $timedAnnotation */
        $timedAnnotation = AnnotationManager::getMethodAnnotations($class, $method, Timed::class)[0];
        $driver = MeterRegistry::getDriverInstance();
        /** @var ITimerSample $timerSample */
        $timerSample = ($driver::TIMER_CLASS)::start();
        try
        {
            $returnValue = $joinPoint->proceed();

            return $returnValue;
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            $context = [
                'params'      => $joinPoint->getArgs(),
                'returnValue' => $returnValue ?? null,
            ];

            $labels = $timedAnnotation->tags;
            foreach ($labels as &$value)
            {
                if (\is_string($value))
                {
                    $value = preg_replace_callback('/\{([^\}]+)\}/', static fn (array $matches): string => (string) ObjectArrayHelper::get($context, $matches[1]), $value);
                }
            }

            $options = MeterRegistry::getOptions();
            if (!Text::isEmpty($resultTag = $options['resultTag'] ?? 'result') && !isset($labels[$resultTag]))
            {
                $labels[$resultTag] = isset($th) ? ($options['resultTagFailureValue'] ?? 'failure') : ($options['resultTagSuccessValue'] ?? 'success');
            }
            if (!Text::isEmpty($exceptionTag = $options['exceptionTag'] ?? 'exception') && !isset($labels[$exceptionTag]))
            {
                $labels[$exceptionTag] = isset($th) ? \get_class($th) : ($options['defaultExceptionTagValue'] ?? 'none');
            }
            if (!Text::isEmpty($instanceTag = $options['instanceTag'] ?? 'instance') && !isset($labels[$instanceTag]))
            {
                $labels[$instanceTag] = $options['instance'] ?? 'imi';
            }
            if (!Text::isEmpty($workerTag = $options['workerTag'] ?? 'worker') && !isset($labels[$workerTag]))
            {
                $labels[$workerTag] = (string) Worker::getWorkerId();
            }

            $timer = MeterRegistry::getDriverInstance()->timer($timedAnnotation->name, $labels, $timedAnnotation->description, $timedAnnotation->baseTimeUnit, $timedAnnotation->options);
            $timerSample->stop($timer);
        }
    }
}
