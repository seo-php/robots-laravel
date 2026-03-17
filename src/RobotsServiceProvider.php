<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel;

use Illuminate\Support\ServiceProvider;

final class RobotsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
