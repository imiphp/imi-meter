<?php

declare(strict_types=1);

namespace Imi\Meter;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Meter\Contract\IMeterRegistry;

/**
 * @Bean("MeterRegistry")
 */
class MeterRegistry
{
    protected string $driver = '';

    protected array $options = [];

    private ?IMeterRegistry $driverInstance = null;

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
        if (!$this->driverInstance)
        {
            if ('' === $this->driver)
            {
                throw new \InvalidArgumentException('Config @app.beans.MeterRegistry.driver cannot be empty');
            }
            // @phpstan-ignore-next-line
            return $this->driverInstance = App::getBean($this->driver, $this->options);
        }

        return $this->driverInstance;
    }
}
