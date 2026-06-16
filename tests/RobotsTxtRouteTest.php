<?php

declare(strict_types=1);

use Seo\Robots\Laravel\RobotsTxtConfig;
use Seo\Robots\Laravel\Tests\TestCase;
use Seo\Robots\RobotsTxtBuilder;
use Seo\Robots\RobotsTxtGroupBuilder;

it('serves configured robots.txt in production', function (): void {
    /** @var TestCase $this */
    configureRobotsTxt(function (RobotsTxtConfig $config): void {
        $config->robots(
            fn () => (new RobotsTxtBuilder())
                ->group(
                    fn (RobotsTxtGroupBuilder $group) => $group
                        ->agent('*')
                        ->allow('/'),
                )
                ->sitemap('https://example.com/sitemap.xml')
                ->build(),
        );
    });

    app()->detectEnvironment(fn () => 'production');

    $response = $this->getRobotsRoute();

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSeeText('User-agent: *')
        ->assertSeeText('Allow: /')
        ->assertSeeText('Sitemap: https://example.com/sitemap.xml')
    ;
});

it('serves configured robots.txt in local', function (): void {
    /** @var TestCase $this */
    configureRobotsTxt(function (RobotsTxtConfig $config): void {
        $config->robots(
            fn () => (new RobotsTxtBuilder())
                ->group(
                    fn (RobotsTxtGroupBuilder $group) => $group
                        ->agent('*')
                        ->allow('/'),
                )
                ->build(),
        );
    });

    app()->detectEnvironment(fn () => 'local');

    $this->getRobotsRoute()
        ->assertOk()
        ->assertSeeText('Allow: /')
    ;
});

it('returns disallow all robots.txt in staging', function (): void {
    /** @var TestCase $this */
    configureRobotsTxt(function (RobotsTxtConfig $config): void {
        $config->robots(
            fn () => (new RobotsTxtBuilder())
                ->group(
                    fn (RobotsTxtGroupBuilder $group) => $group
                        ->agent('*')
                        ->allow('/'),
                )
                ->build(),
        );
    });

    app()->detectEnvironment(fn () => 'staging');

    $this->getRobotsRoute()
        ->assertOk()
        ->assertSeeText('Disallow: /')
        ->assertDontSeeText('Allow: /')
    ;
});

it('returns disallow all robots.txt in testing', function (): void {
    /** @var TestCase $this */
    configureRobotsTxt(function (RobotsTxtConfig $config): void {
        $config->robots(
            fn () => (new RobotsTxtBuilder())
                ->group(
                    fn (RobotsTxtGroupBuilder $group) => $group
                        ->agent('*')
                        ->allow('/'),
                )
                ->build(),
        );
    });

    app()->detectEnvironment(fn () => 'testing');

    $this->getRobotsRoute()
        ->assertOk()
        ->assertSeeText('Disallow: /')
    ;
});

it('returns disallow all when robots is not configured', function (): void {
    /** @var TestCase $this */
    configureRobotsTxt(function (RobotsTxtConfig $config): void {
        $config->domain(null);
    });

    app()->detectEnvironment(fn () => 'production');

    $this->getRobotsRoute()
        ->assertOk()
        ->assertSeeText('Disallow: /')
    ;
});

it('serves domain-specific robots.txt', function (): void {
    /** @var TestCase $this */
    configureRobotsTxt(function (RobotsTxtConfig $config): void {
        $config
            ->domain('marketing.example.com')
            ->robots(
                fn () => (new RobotsTxtBuilder())
                    ->group(
                        fn (RobotsTxtGroupBuilder $group) => $group
                            ->agent('*')
                            ->disallow('/private'),
                    )
                    ->build(),
            )
        ;
    });

    app()->detectEnvironment(fn () => 'production');

    $this->getRobotsRoute(host: 'marketing.example.com')
        ->assertOk()
        ->assertSeeText('Disallow: /private')
    ;
});
