<?php

declare(strict_types=1);

namespace Imi\Meter\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Meter\Enum\GaugeOperation;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * @property string       $name
 * @property bool         $recordFailuresOnly
 * @property array        $tags
 * @property string       $description
 * @property string|float $value
 * @property int          $operation          \Imi\Meter\Enum\GaugeOperation::XXX
 * @property array        $options
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Gauged extends Base
{
    /**
     * @param string|float $value
     */
    public function __construct(?array $__data = null, string $name = 'imi.counted', bool $recordFailuresOnly = false, array $tags = [], string $description = '', $value = '{returnValue}', int $operation = GaugeOperation::SET, array $options = [])
    {
        parent::__construct(...\func_get_args());
    }
}
