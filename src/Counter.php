<?php

declare(strict_types=1);

namespace Imi\Meter;

use Imi\Meter\Contract\ICounter;
use Imi\Meter\Contract\IMeterRegistry;
use Imi\Meter\Enum\MeterType;
use Imi\Meter\Traits\TMeter;

class Counter implements ICounter
{
    use TMeter;

    protected float $value = 0;

    public function __construct(string $name, array $tags = [], string $description = '', array $options = [], ?IMeterRegistry $meterRegistry = null)
    {
        $this->name = $name;
        $this->tags = $tags;
        $this->description = $description;
        $this->options = $options;
        $this->meterRegistry = $meterRegistry;
    }

    public function getType(): string
    {
        return MeterType::COUNTER;
    }

    public function increment(float $amount = 1): void
    {
        $this->value += $amount;
    }

    public function value(): float
    {
        return $this->value;
    }
}
