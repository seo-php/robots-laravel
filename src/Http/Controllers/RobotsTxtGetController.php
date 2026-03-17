<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Seo\Robots\Laravel\Http\Responses\RobotsTxtResponse;
use Seo\Robots\Laravel\RobotsTxtConfigRegistry;
use Seo\Robots\RobotsTxt;
use Seo\Robots\RobotsTxtDirective;
use Seo\Robots\RobotsTxtGroup;

final readonly class RobotsTxtGetController
{
    public function __construct(
        private RobotsTxtConfigRegistry $registry,
        private Application $app,
    ) {}

    public function __invoke(Request $request): RobotsTxtResponse
    {
        $config = $this->registry->resolve($request->getHost());

        $robots = $config?->robots !== null && $this->app->environment(['production', 'local'])
            ? ($config->robots)()
            : $this->createDisallowAll();

        return new RobotsTxtResponse($robots);
    }

    private function createDisallowAll(): RobotsTxt
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
