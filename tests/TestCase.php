<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel\Tests;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Seo\Robots\Laravel\Providers\RobotsTxtServiceProvider as AppRobotsTxtServiceProvider;
use Seo\Robots\Laravel\RobotsServiceProvider;
use Seo\Robots\Laravel\RobotsTxtConfig;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @method \Illuminate\Testing\PendingCommand artisan(string $command, array<string, mixed> $parameters = [])
 */
abstract class TestCase extends OrchestraTestCase
{
    public static ?self $current = null;

    /**
     * @var ?Closure(RobotsTxtConfig): void
     */
    public static ?Closure $configureUsing = null;

    /**
     * @param Closure(RobotsTxtConfig): void $configure
     */
    final public static function configureRobotsTxt(Closure $configure): void
    {
        self::$configureUsing = $configure;
    }

    /**
     * @param Closure(RobotsTxtConfig): void $configure
     */
    final public function configureRobotsTxtForTest(Closure $configure): void
    {
        self::$configureUsing = $configure;
        $this->refreshApplication();
    }

    protected function setUp(): void
    {
        self::$current = $this;

        parent::setUp();
    }

    protected function tearDown(): void
    {
        self::$configureUsing = null;
        self::$current = null;

        parent::tearDown();
    }

    /**
     * @return TestResponse<SymfonyResponse>
     */
    final public function getRobotsRoute(string $path = '/robots.txt', string $host = 'example.com'): TestResponse
    {
        $app = $this->app;
        assert($app !== null);

        $response = $app->handle(Request::create($path, 'GET', server: ['HTTP_HOST' => $host]));

        return TestResponse::fromBaseResponse($response);
    }

    protected function getPackageProviders($app): array
    {
        return [
            RobotsServiceProvider::class,
            TestRobotsTxtServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        /** @var Repository $config */
        $config = $app->make('config');
        $config->set('app.url', 'https://example.com');
    }
}

final class TestRobotsTxtServiceProvider extends AppRobotsTxtServiceProvider
{
    protected function configure(RobotsTxtConfig $config): void
    {
        if (TestCase::$configureUsing !== null) {
            (TestCase::$configureUsing)($config);
        }
    }
}
