<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

use Imi\Meter\Counter;
use Imi\Meter\Enum\TimeUnit;
use Imi\Meter\Gauge;
use Imi\Meter\Histogram;
use Imi\Meter\Summary;
use Imi\Meter\Timer;

interface IMeterRegistry
{
    public const COUNTER_CLASS = Counter::class;

    public const GAUGE_CLASS = Gauge::class;

    public const TIMER_CLASS = Timer::class;

    public const HISTOGRAM_CLASS = Histogram::class;

    public const SUMMARY_CLASS = Summary::class;

    public function counter(string $name, array $tags = [], string $description = '', array $options = []): ICounter;

    public function gauge(string $name, array $tags = [], string $description = '', array $options = []): IGauge;

    public function timer(string $name, array $tags = [], string $description = '', int $baseTimeUnit = TimeUnit::NANO_SECOND, array $options = []): ITimer;

    public function histogram(string $name, array $tags = [], string $description = '', ?array $buckets = null, array $options = []): IHistogram;

    public function summary(string $name, array $tags = [], string $description = '', ?array $percentile = [], array $options = []): ISummary;

    public function getMeters(): array;

    public function remove(IMeter $meter): void;

    public function close(): void;

    public function isClosed(): bool;

    public function getConfig(): array;
}
