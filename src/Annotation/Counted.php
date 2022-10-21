<?php

declare(strict_types=1);

namespace Imi\Meter\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * @property string $name
 * @property bool   $recordFailuresOnly
 * @property array  $tags
 * @property string $description
 * @property array  $options
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Counted extends Base
{
    public function __construct(?array $__data = null, string $name = 'imi.counted', bool $recordFailuresOnly = false, array $tags = [], string $description = '', array $options = [])
    {
        parent::__construct(...\func_get_args());
    }
}
