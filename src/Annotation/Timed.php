<?php

declare(strict_types=1);

namespace Imi\Meter\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Meter\Enum\TimeUnit;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * @property string $name
 * @property array  $tags
 * @property string $description
 * @property int    $baseTimeUnit
 * @property array  $options
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Timed extends Base
{
    public function __construct(?array $__data = null, string $name = '', array $tags = [], string $description = '', int $baseTimeUnit = TimeUnit::NANO_SECOND, array $options = [])
    {
        parent::__construct(...\func_get_args());
    }
}
