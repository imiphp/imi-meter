<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

interface IGauge extends IMeter
{
    public function record(float $value): void;

    public function increment(float $amount = 1): void;

    public function decrement(float $amount = 1): void;

    public function value(): float;
}
