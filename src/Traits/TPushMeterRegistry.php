<?php

declare(strict_types=1);

namespace Imi\Meter\Traits;

use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Timer\Timer;
use Throwable;

trait TPushMeterRegistry
{
    private ?int $timerId = null;

    abstract public function publish(): void;

    public function start(): void
    {
        $interval = $this->getConfig()['interval'] ?? 0;
        if ($interval > 0)
        {
            $this->timerId = Timer::tick((int) ($interval * 1000), fn () => $this->publishSafely());
            Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.MAIN_SERVER.WORKER.STOP', 'IMI.PROCESS.END'], fn () => $this->stop(), \Imi\Util\ImiPriority::IMI_MIN);
        }
    }

    public function stop(): void
    {
        if (null !== $this->timerId)
        {
            Timer::del($this->timerId);
            $this->timerId = null;
        }
    }

    public function close(): void
    {
        if (($this->getConfig()['interval'] ?? 0) > 0)
        {
            $this->publishSafely();
        }
        $this->stop();
        parent::close();
    }

    private function publishSafely(): void
    {
        static $publishing = false;
        if ($publishing)
        {
            return;
        }
        $publishing = true;
        try
        {
            $this->publish();
        }
        catch (Throwable $th)
        {
            Log::error($th);
        }
        finally
        {
            $publishing = false;
        }
    }
}
