<?php

declare(strict_types=1);

namespace Imi\Meter;

use Imi\Meter\Contract\ITimer;
use Imi\Meter\Contract\ITimerSample;

class TimerSample implements ITimerSample
{
    private int $startTime = 0;

    public function start(int $time = 0): void
    {
        $this->startTime = $time ?: hrtime(true);
    }

    public function stop(ITimer $timer, int $time = 0): int
    {
        $duration = ($time ?: hrtime(true)) - $this->startTime;
        $timer->record($duration);

        return $duration;
    }
}
