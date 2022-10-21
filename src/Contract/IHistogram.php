<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

interface IHistogram extends IMeter
{
    public function record(float $value): void;

    public function count(): int;

    public function totalAmount(): float;

    public function mean(): float;

    public function getBuckets(): ?array;
}
