<?php

declare(strict_types=1);

namespace MicroPHP\RoadRunner;

use MicroPHP\Framework\Config\ConfigProviderInterface;
use MicroPHP\RoadRunner\Commands\RoadRunnerInstallCommand;

class ConfigProvider implements ConfigProviderInterface
{
    public function config(): array
    {
        return [
            'commands' => [
                RoadRunnerInstallCommand::class,
            ],
        ];
    }
}
