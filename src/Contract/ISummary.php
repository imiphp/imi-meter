<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

interface ISummary extends IMeter
{
    public function record(float $value): void;

    public function count(): int;

    public function totalAmount(): float;

    public function mean(): float;

    public function getPercentile(): ?array;
}
