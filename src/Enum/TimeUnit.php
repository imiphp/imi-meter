<?php

declare(strict_types=1);

namespace Imi\Meter\Enum;

class TimeUnit
{
    public const SECOND = 1;

    public const MILLI_SECONDS = 1000;

    public const MICRO_SECOND = 1000_000;

    public const NANO_SECOND = 1000_000_000;

    private function __construct()
    {
    }
}
