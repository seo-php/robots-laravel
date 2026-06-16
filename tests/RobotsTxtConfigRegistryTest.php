<?php

declare(strict_types=1);

use Seo\Robots\Laravel\RobotsTxtConfig;
use Seo\Robots\Laravel\RobotsTxtConfigRegistry;
use Seo\Robots\RobotsTxtBuilder;
use Seo\Robots\RobotsTxtGroupBuilder;

it('registers and resolves configs by domain', function (): void {
    $registry = new RobotsTxtConfigRegistry();

    $global = RobotsTxtConfig::make()
        ->robots(
            fn () => (new RobotsTxtBuilder())
                ->group(
                    fn (RobotsTxtGroupBuilder $group) => $group
                        ->agent('*')
                        ->allow('/'),
                )
                ->build(),
        )
    ;

    $marketing = RobotsTxtConfig::make()
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

    $registry->register($global);
    $registry->register($marketing);

    expect($registry->resolve('example.com'))->toBe($global)
        ->and($registry->resolve('marketing.example.com'))->toBe($marketing)
        ->and($registry->resolve('unknown.example.com'))->toBe($global)
        ->and($registry->all())->toHaveCount(2)
    ;
});

it('returns null when no config matches and no global config exists', function (): void {
    $registry = new RobotsTxtConfigRegistry();

    $marketing = RobotsTxtConfig::make()
        ->domain('marketing.example.com')
        ->robots(
            fn () => (new RobotsTxtBuilder())
                ->group(
                    fn (RobotsTxtGroupBuilder $group) => $group
                        ->agent('*')
                        ->allow('/'),
                )
                ->build(),
        )
    ;

    $registry->register($marketing);

    expect($registry->resolve('example.com'))->toBeNull()
        ->and($registry->resolve('marketing.example.com'))->toBe($marketing)
    ;
});
