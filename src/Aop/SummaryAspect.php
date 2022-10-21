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
use Imi\Meter\Annotation\Summary;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Util\ObjectArrayHelper;

/**
 * @Aspect
 */
class SummaryAspect
{
    /**
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Summary::class
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
        /** @var Summary $summaryAnnotation */
        $summaryAnnotation = AnnotationManager::getMethodAnnotations($class, $method, Summary::class)[0];
        $returnValue = $joinPoint->proceed();

        $context = [
            'params'      => $joinPoint->getArgs(),
            'returnValue' => $returnValue ?? null,
        ];

        $labels = $summaryAnnotation->tags;
        foreach ($labels as &$value)
        {
            if (\is_string($value))
            {
                $value = preg_replace_callback('/\{([^\}]+)\}/', static fn (array $matches): string => (string) ObjectArrayHelper::get($context, $matches[1]), $value);
            }
        }

        unset($value);

        if (\is_string($summaryAnnotation->value))
        {
            $value = (float) preg_replace_callback('/\{([^\}]+)\}/', static fn (array $matches): string => (string) ObjectArrayHelper::get($context, $matches[1]), $summaryAnnotation->value);
        }
        else
        {
            $value = $summaryAnnotation->value;
        }

        MeterRegistry::getDriverInstance()->summary($summaryAnnotation->name, $labels, $summaryAnnotation->description, $summaryAnnotation->percentile, $summaryAnnotation->options)->record($value);

        return $returnValue;
    }
}
