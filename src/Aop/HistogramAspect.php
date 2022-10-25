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
use Imi\Meter\Annotation\Histogram;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Text;
use Imi\Worker;

/**
 * @Aspect
 */
class HistogramAspect
{
    /**
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Histogram::class
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
        /** @var Histogram $histogramAnnotation */
        $histogramAnnotation = AnnotationManager::getMethodAnnotations($class, $method, Histogram::class)[0];
        $returnValue = $joinPoint->proceed();

        $context = [
            'params'      => $joinPoint->getArgs(),
            'returnValue' => $returnValue ?? null,
        ];

        $labels = $histogramAnnotation->tags;
        foreach ($labels as &$value)
        {
            if (\is_string($value))
            {
                $value = preg_replace_callback('/\{([^\}]+)\}/', static fn (array $matches): string => (string) ObjectArrayHelper::get($context, $matches[1]), $value);
            }
        }
        if (!Text::isEmpty($instanceTag = $options['instanceTag'] ?? 'instance') && !isset($labels[$instanceTag]))
        {
            $labels[$instanceTag] = $options['instance'] ?? 'imi';
        }
        if (!Text::isEmpty($workerTag = $options['workerTag'] ?? 'worker') && !isset($labels[$workerTag]))
        {
            $labels[$workerTag] = (string) Worker::getWorkerId();
        }

        unset($value);

        if (\is_string($histogramAnnotation->value))
        {
            $value = (float) preg_replace_callback('/\{([^\}]+)\}/', static fn (array $matches): string => (string) ObjectArrayHelper::get($context, $matches[1]), $histogramAnnotation->value);
        }
        else
        {
            $value = $histogramAnnotation->value;
        }

        MeterRegistry::getDriverInstance()->histogram($histogramAnnotation->name, $labels, $histogramAnnotation->description, $histogramAnnotation->buckets, $histogramAnnotation->options)->record($value);

        return $returnValue;
    }
}
