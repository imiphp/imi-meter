<?php

declare(strict_types=1);

namespace Imi\Meter;

use Imi\Main\BaseMain;

class Main extends BaseMain
{
    public function __init(): void
    {
        if (4 == \PHP_INT_SIZE)
        {
            throw new \RuntimeException('imiphp/imi-meter does not support 32-bit PHP, please use 64-bit PHP.');
        }
    }
}
