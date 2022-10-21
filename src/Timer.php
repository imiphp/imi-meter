<?php

declare(strict_types=1);

namespace Imi\Meter;

use Imi\Meter\Contract\IMeterRegistry;
use Imi\Meter\Contract\ITimer;
use Imi\Meter\Contract\ITimerSample;
use Imi\Meter\Enum\MeterType;
use Imi\Meter\Enum\TimeUnit;
use Imi\Meter\Traits\TMeter;

abstract class Timer implements ITimer
{
    use TMeter;

    protected int $baseTimeUnit = TimeUnit::NANO_SECOND;

    public function __construct(string $name, array $tags = [], string $description = '', int $baseTimeUnit = TimeUnit::NANO_SECOND, array $options = [], ?IMeterRegistry $meterRegistry = null)
    {
        $this->name = $name;
        $this->tags = $tags;
        $this->description = $description;
        $this->baseTimeUnit = $baseTimeUnit;
        $this->options = $options;
        $this->meterRegistry = $meterRegistry;
    }

    public static function start(int $nanoSecond = 0): ITimerSample
    {
        $sample = new TimerSample();
        $sample->start($nanoSecond);

        return $sample;
    }

    public function getType(): string
    {
        return MeterType::TIMER;
    }

    /**
     * @return mixed
     */
    public function recordCallable(callable $callable)
    {
        $sample = new TimerSample();
        $sample->start();
        try
        {
            return $callable();
        }
        finally
        {
            $sample->stop($this);
        }
    }

    public function baseTimeUnit(): int
    {
        return $this->baseTimeUnit;
    }
}
