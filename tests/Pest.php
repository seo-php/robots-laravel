<?php

declare(strict_types=1);

use Seo\Robots\Laravel\RobotsTxtConfig;
use Seo\Robots\Laravel\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * @param Closure(RobotsTxtConfig): void $configure
 */
function configureRobotsTxt(Closure $configure): void
{
    if (TestCase::$current instanceof TestCase) {
        TestCase::$current->configureRobotsTxtForTest($configure);

        return;
    }

    TestCase::configureRobotsTxt($configure);
}
