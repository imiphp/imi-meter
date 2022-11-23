<?php

declare(strict_types=1);

namespace Imi\Meter\Monitor;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Pool\PoolManager;
use Imi\Timer\Timer;
use Imi\Util\ImiPriority;
use Imi\Worker;

/**
 * 连接池指标监控.
 *
 * @Bean("PoolMonitor")
 */
class PoolMonitor
{
    /**
     * 是否已启用.
     */
    protected bool $enable = false;

    /**
     * 监控的连接池名称数组.
     *
     * 如果为 null 则代表监控所有连接池
     *
     * @var string[]|null
     */
    protected ?array $pools = null;

    /**
     * 上报时间间隔，单位：秒.
     */
    protected float $interval = 10;

    protected string $countKey = 'pool_count';

    protected string $usedKey = 'pool_used';

    protected string $freeKey = 'pool_free';

    protected string $workerIdTag = 'worker_id';

    protected string $poolNameTag = 'pool_name';

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
            try
            {
                $driver = MeterRegistry::getDriverInstance();
                $tags = [
                    $this->workerIdTag => Worker::getWorkerId(),
                ];
                foreach ($this->pools ?? PoolManager::getNames() as $poolName)
                {
                    $pool = PoolManager::getInstance($poolName);
                    $tags[$this->poolNameTag] = $poolName;
                    $driver->gauge($this->countKey, $tags)->record($pool->getCount());
                    $driver->gauge($this->usedKey, $tags)->record($pool->getUsed());
                    $driver->gauge($this->freeKey, $tags)->record($pool->getFree());
                }
            }
            catch (\Throwable $th)
            {
                Log::error($th);
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
     * @return string[]|null
     */
    public function getPools(): ?array
    {
        return $this->pools;
    }

    /**
     * Get 上报时间间隔，单位：秒.
     */
    public function getInterval(): float
    {
        return $this->interval;
    }
}
