<?php

declare(strict_types=1);

namespace Imi\Meter\Util;

class TimeUnitUtil
{
    private function __construct()
    {
    }

    /**
     * @return int|float
     */
    public static function convert(int $timestamp, int $srcTimeUnit, int $destTimeUnit)
    {
        return $timestamp * ($destTimeUnit / $srcTimeUnit);
    }
}
