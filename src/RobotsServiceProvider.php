<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel;

use Illuminate\Support\ServiceProvider;
use Seo\Robots\Laravel\Console\Commands;

final class RobotsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singletonIf(RobotsTxtConfigRegistry::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    private function bootForConsole(): void
    {
        $this->commands([
            Commands\InstallCommand::class,
        ]);
    }
}
