<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

interface ICounter extends IMeter
{
    public function increment(float $amount = 1): void;

    public function value(): float;
}
