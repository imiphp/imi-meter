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
use Imi\Meter\Annotation\Counted;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Text;

/**
 * @Aspect
 */
class CountedAspect
{
    // public final String DEFAULT_EXCEPTION_TAG_VALUE = "none";

    // public final String RESULT_TAG_FAILURE_VALUE = "failure";

    // public final String RESULT_TAG_SUCCESS_VALUE = "success";

    // /**
    //  * The tag name to encapsulate the method execution status.
    //  */
    // private static final String RESULT_TAG = "result";

    // /**
    //  * The tag name to encapsulate the exception thrown by the intercepted method.
    //  */
    // private static final String EXCEPTION_TAG = "exception";

    /**
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Counted::class
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
        /** @var Counted $countedAnnotation */
        $countedAnnotation = AnnotationManager::getMethodAnnotations($class, $method, Counted::class)[0];
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
            if (isset($th) || !$countedAnnotation->recordFailuresOnly)
            {
                $context = [
                    'params'      => $joinPoint->getArgs(),
                    'returnValue' => $returnValue ?? null,
                ];

                $labels = $countedAnnotation->tags;
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

                MeterRegistry::getDriverInstance()->counter($countedAnnotation->name, $labels, $countedAnnotation->description)->increment();
            }
        }
    }
}
