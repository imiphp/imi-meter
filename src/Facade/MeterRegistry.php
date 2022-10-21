<?php

declare(strict_types=1);

namespace Imi\Meter\Facade;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @Facade(class="MeterRegistry", request=false, args={})
 *
 * @method static string getDriver()
 * @method static array getOptions()
 * @method static \Imi\Meter\Contract\IMeterRegistry getDriverInstance()
 */
class MeterRegistry extends BaseFacade
{
}
