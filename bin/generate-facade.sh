#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__/../

vendor/bin/imi-cli --app-namespace "Imi\Meter" generate/facade "Imi\Meter\Facade\MeterRegistry" "MeterRegistry" && \

vendor/bin/php-cs-fixer fix
