<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Seo\Robots\Laravel\RobotsTxtConfig;
use Seo\Robots\Laravel\RobotsTxtConfigRegistry;

abstract class RobotsTxtServiceProvider extends ServiceProvider
{
    final public function register(): void
    {
        $this->app->afterResolving(RobotsTxtConfigRegistry::class, function (RobotsTxtConfigRegistry $registry): void {
            $config = RobotsTxtConfig::make();

            $this->configure($config);

            $registry->register($config);
        });
    }

    abstract protected function configure(RobotsTxtConfig $config): void;
}
