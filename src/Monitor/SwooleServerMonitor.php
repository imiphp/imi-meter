<?php

declare(strict_types=1);

namespace Imi\Meter\Monitor;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Timer\Timer;
use Imi\Util\ImiPriority;
use Imi\Worker;

/**
 * Swoole 服务器指标监控.
 *
 * @Bean("SwooleServerMonitor")
 */
class SwooleServerMonitor
{
    /**
     * 是否已启用.
     */
    protected bool $enable = false;

    /**
     * 要监控的指标名称数组.
     *
     * @see https://wiki.swoole.com/#/server/methods?id=stats
     */
    protected array $stats = [];

    /**
     * 上报时间间隔，单位：秒.
     */
    protected float $interval = 10;

    protected string $workerIdTag = 'worker_id';

    private ?int $timerId = null;

    public function __init(): void
    {
        if ($this->enable)
        {
            $this->listen();
        }
    }

    protected function listen(): void
    {
        Event::one('IMI.SERVER.WORKER_START', [$this, 'run']);
        Event::on('IMI.MAIN_SERVER.WORKER.EXIT', [$this, 'stop'], ImiPriority::IMI_MAX);
    }

    public function run(): void
    {
        $this->timerId = Timer::tick((int) ($this->interval * 1000), function () {
            if ($this->stats)
            {
                /** @var ISwooleServer $server */
                $server = ServerManager::getServer('main', ISwooleServer::class);
                $swooleServer = $server->getSwooleServer();
                $stats = $swooleServer->stats();
                $driver = MeterRegistry::getDriverInstance();
                $tags = [
                    $this->workerIdTag => Worker::getWorkerId(),
                ];
                foreach ($this->stats as $key => $value)
                {
                    if (\is_string($key))
                    {
                        $statsName = $key;
                        $meterName = $value;
                    }
                    else
                    {
                        $statsName = $meterName = $value;
                    }
                    if (isset($stats[$statsName]))
                    {
                        $driver->gauge($meterName, $tags)->record($stats[$statsName]);
                    }
                }
            }
        });
    }

    public function stop(): void
    {
        if (null !== $this->timerId)
        {
            Timer::del($this->timerId);
        }
    }

    /**
     * 是否已启用.
     */
    public function isEnabled(): bool
    {
        return $this->enable;
    }

    /**
     * Get 要监控的指标名称数组.
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Get 上报时间间隔，单位：秒.
     */
    public function getInterval(): float
    {
        return $this->interval;
    }
}
