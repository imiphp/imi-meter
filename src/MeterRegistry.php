<?php

declare(strict_types=1);

namespace Imi\Meter;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Meter\Contract\IMeterRegistry;
use Imi\RequestContext;

/**
 * @Bean("MeterRegistry")
 */
class MeterRegistry
{
    public const CONTEXT_KEY = self::class . '.driverInstance';

    protected string $driver = '';

    protected array $options = [];

    public function __construct()
    {
        Event::on('IMI.REQUEST_CONTENT.DESTROY', fn () => $this->onContextDestroy());
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getDriverInstance(): IMeterRegistry
    {
        $context = RequestContext::getContext();
        if (isset($context[self::CONTEXT_KEY]))
        {
            return $context[self::CONTEXT_KEY];
        }
        if ('' === $this->driver)
        {
            throw new \InvalidArgumentException('Config @app.beans.MeterRegistry.driver cannot be empty');
        }
        /** @var IMeterRegistry $instance */
        // @phpstan-ignore-next-line
        $instance = App::newInstance($this->driver, $this->options);
        RequestContext::set(self::CONTEXT_KEY, $instance);

        return $instance;
    }

    protected function onContextDestroy(): void
    {
        if ($instance = RequestContext::get(self::CONTEXT_KEY))
        {
            /** @var IMeterRegistry $instance */
            $instance->close();
        }
    }
}
