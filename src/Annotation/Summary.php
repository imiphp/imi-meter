<?php

declare(strict_types=1);

namespace Imi\Meter\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Meter\Enum\TimeUnit;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * @property string       $name
 * @property array        $tags
 * @property string       $description
 * @property array|null   $quantiles
 * @property int          $baseTimeUnit
 * @property string|float $value
 * @property array        $options
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Summary extends Base
{
    /**
     * @param float[]      $quantiles
     * @param string|float $value
     */
    public function __construct(?array $__data = null, string $name = '', array $tags = [], string $description = '', ?array $quantiles = [], int $baseTimeUnit = TimeUnit::NANO_SECOND, $value = '{returnValue}', array $options = [])
    {
        parent::__construct(...\func_get_args());
    }
}
