<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

interface ITimer extends IMeter
{
    public static function start(int $nanoSecond = 0): ITimerSample;

    public function record(int $nanoSecond, ?int $timeUnit = null): void;

    /**
     * @return mixed
     */
    public function recordCallable(callable $callable);

    public function baseTimeUnit(): int;
}
