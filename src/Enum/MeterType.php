<?php

declare(strict_types=1);

namespace Imi\Meter\Enum;

class MeterType
{
    public const COUNTER = 'counter';

    public const GAUGE = 'gauge';

    public const HISTOGRAM = 'histogram';

    public const DISTRIBUTION_SUMMARY = 'distribution_summary';

    public const TIMER = 'timer';

    private function __construct()
    {
    }
}
