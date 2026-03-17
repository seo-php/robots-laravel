<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Seo\Robots\Laravel\Http\Controllers\RobotsTxtGetController;
use Seo\Robots\RobotsTxt;
use Seo\Robots\RobotsTxtDirective;
use Seo\Robots\RobotsTxtGroup;

abstract class RobotsTxtServiceProvider extends ServiceProvider
{
    final public function register(): void
    {
        $this->app->bindIf(RobotsTxtGetController::class, function (Application $app) {
            $robots = $app->environment(['production', 'local'])
                ? $this->robots()
                : $this->createForNonProduction();

            return new RobotsTxtGetController($robots);
        });
    }

    abstract protected function robots(): RobotsTxt;

    private function createForNonProduction(): RobotsTxt
    {
        return new RobotsTxt(
            groups: [
                new RobotsTxtGroup(
                    agents: ['*'],
                    directives: [
                        new RobotsTxtDirective('Disallow', '/'),
                    ],
                ),
            ],
            sitemaps: [],
        );
    }
}
