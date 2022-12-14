<?php

declare(strict_types=1);

namespace Imi\Meter;

use Imi\Meter\Contract\IMeterRegistry;
use Imi\Meter\Contract\ISummary;
use Imi\Meter\Enum\MeterType;
use Imi\Meter\Traits\TMeter;

class Summary implements ISummary
{
    use TMeter;

    protected float $value = 0;

    protected int $count = 0;

    protected ?array $percentile = null;

    public function __construct(string $name, array $tags = [], string $description = '', ?array $percentile = null, array $options = [], ?IMeterRegistry $meterRegistry = null)
    {
        $this->name = $name;
        $this->tags = $tags;
        $this->description = $description;
        $this->percentile = $percentile;
        $this->options = $options;
        $this->meterRegistry = $meterRegistry;
    }

    public function getType(): string
    {
        return MeterType::DISTRIBUTION_SUMMARY;
    }

    public function record(float $value): void
    {
        $this->value += $value;
        ++$this->count;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function totalAmount(): float
    {
        return $this->value;
    }

    public function mean(): float
    {
        $count = $this->count();

        return 0 === $count ? 0 : ($this->totalAmount() / $count);
    }

    public function getPercentile(): ?array
    {
        return $this->percentile;
    }
}
