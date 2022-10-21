<?php

declare(strict_types=1);

namespace Imi\Meter\Traits;

use Imi\Meter\Contract\IMeterRegistry;

trait TMeter
{
    protected string $name = '';

    protected array $tags = [];

    protected string $description = '';

    protected ?IMeterRegistry $meterRegistry = null;

    protected array $options = [];

    public function getMeterRegistry(): ?IMeterRegistry
    {
        return $this->meterRegistry;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function close(): void
    {
    }
}
