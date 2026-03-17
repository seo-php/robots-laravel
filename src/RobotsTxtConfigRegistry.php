<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel;

final class RobotsTxtConfigRegistry
{
    private const string GLOBAL = '';

    /**
     * @var array<string, RobotsTxtConfig>
     */
    private array $configs = [];

    public function register(RobotsTxtConfig $config): void
    {
        $this->configs[$config->domain ?? self::GLOBAL] = $config;
    }

    public function resolve(string $domain): ?RobotsTxtConfig
    {
        return $this->configs[$domain] ?? $this->configs[self::GLOBAL] ?? null;
    }

    /**
     * @return array<string, RobotsTxtConfig>
     */
    public function all(): array
    {
        return $this->configs;
    }
}
