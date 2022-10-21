<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

use Imi\App;
use Imi\Meter\Enum\TimeUnit;

abstract class BaseMeterRegistry implements IMeterRegistry
{
    protected array $meters = [];

    protected array $config = [];

    private bool $closed = false;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function counter(string $name, array $tags = [], string $description = '', array $options = []): ICounter
    {
        // @phpstan-ignore-next-line
        return $this->meters[$this->generateKey(static::COUNTER_CLASS, $name, $tags)] ??= App::newInstance(static::COUNTER_CLASS, $name, $tags, $description, $options, $this);
    }

    public function gauge(string $name, array $tags = [], string $description = '', array $options = []): IGauge
    {
        // @phpstan-ignore-next-line
        return $this->meters[$this->generateKey(static::GAUGE_CLASS, $name, $tags)] ??= App::newInstance(static::GAUGE_CLASS, $name, $tags, $description, $options, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function timer(string $name, array $tags = [], string $description = '', int $baseTimeUnit = TimeUnit::NANO_SECOND, array $options = []): ITimer
    {
        // @phpstan-ignore-next-line
        return $this->meters[$this->generateKey(static::TIMER_CLASS, $name, $tags)] ??= App::newInstance(static::TIMER_CLASS, $name, $tags, $description, $baseTimeUnit, $options, $this);
    }

    public function histogram(string $name, array $tags = [], string $description = '', ?array $buckets = null, array $options = []): IHistogram
    {
        // @phpstan-ignore-next-line
        return $this->meters[$this->generateKey(static::HISTOGRAM_CLASS, $name, $tags)] ??= App::newInstance(static::HISTOGRAM_CLASS, $name, $tags, $description, $buckets, $options, $this);
    }

    public function summary(string $name, array $tags = [], string $description = '', ?array $quantiles = [], array $options = []): ISummary
    {
        // @phpstan-ignore-next-line
        return $this->meters[$this->generateKey(static::SUMMARY_CLASS, $name, $tags)] ??= App::newInstance(static::SUMMARY_CLASS, $name, $tags, $description, $quantiles, $options, $this);
    }

    public function getMeters(): array
    {
        return array_merge($this->meters);
    }

    public function remove(IMeter $meter): void
    {
        unset($this->meters[$this->generateKey(\get_class($meter), $meter->getName(), $meter->getTags())]);
    }

    protected function generateKey(string $className, string $name, array $tags): string
    {
        return $className . ';' . $name . ';' . http_build_query($tags);
    }

    public function close(): void
    {
        if (!$this->closed)
        {
            $this->closed = true;
            /** @var IMeter $meter */
            foreach ($this->meters as $meter)
            {
                $meter->close();
            }
        }
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
