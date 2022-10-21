<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

interface ITimerSample
{
    public function start(int $time = 0): void;

    public function stop(ITimer $timer, int $time = 0): int;
}
