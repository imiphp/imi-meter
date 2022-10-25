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
use Imi\Log\Log;
use Imi\Meter\Annotation\Gauged;
use Imi\Meter\Enum\GaugeOperation;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Text;
use Imi\Worker;

/**
 * @Aspect
 */
class GaugedAspect
{
    /**
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Gauged::class
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
        /** @var Gauged $gaugedAnnotation */
        $gaugedAnnotation = AnnotationManager::getMethodAnnotations($class, $method, Gauged::class)[0];
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
            if (isset($th) || !$gaugedAnnotation->recordFailuresOnly)
            {
                $context = [
                    'params'      => $joinPoint->getArgs(),
                    'returnValue' => $returnValue ?? null,
                ];

                $labels = $gaugedAnnotation->tags;
                foreach ($labels as &$value)
                {
                    if (\is_string($value))
                    {
                        $value = preg_replace_callback('/\{([^\}]+)\}/', static fn (array $matches): string => (string) ObjectArrayHelper::get($context, $matches[1]), $value);
                    }
                }

                unset($value);

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

                if (\is_string($gaugedAnnotation->value))
                {
                    $value = (float) preg_replace_callback('/\{([^\}]+)\}/', static fn (array $matches): string => (string) ObjectArrayHelper::get($context, $matches[1]), $gaugedAnnotation->value);
                }
                else
                {
                    $value = $gaugedAnnotation->value;
                }

                $gauged = MeterRegistry::getDriverInstance()->gauge($gaugedAnnotation->name, $labels, $gaugedAnnotation->description);
                switch ($gaugedAnnotation->operation)
                {
                    case GaugeOperation::SET:
                        $gauged->record($value);
                        break;
                    case GaugeOperation::INCREMENT:
                        $gauged->increment($value);
                        break;
                    case GaugeOperation::DECREMENT:
                        $gauged->decrement($value);
                        break;
                    default:
                        Log::warning(sprintf('Invalid GaugeOperation %s', $gaugedAnnotation->operation));
                }
            }
        }
    }
}
