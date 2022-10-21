<?php

declare(strict_types=1);

namespace Imi\Meter\Enum;

class GaugeOperation
{
    public const SET = 1;

    public const INCREMENT = 2;

    public const DECREMENT = 3;

    private function __construct()
    {
    }
}
