<?php

declare(strict_types=1);

namespace Imi\Meter\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\ImiPriority;

/**
 * @Listener(eventName="IMI.APP_RUN", priority=ImiPriority::IMI_MAX, one=true)
 */
class AppRunListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        App::getBean('PoolMonitor');
    }
}
