<?php

declare(strict_types=1);

namespace Imi\Meter\Contract;

interface IMeter
{
    public function getMeterRegistry(): ?IMeterRegistry;

    public function getType(): string;

    public function getName(): string;

    public function getTags(): array;

    public function getDescription(): string;

    public function getOptions(): array;

    public function close(): void;
}
