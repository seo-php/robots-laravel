<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel;

use Closure;
use Seo\Robots\RobotsTxt;

final class RobotsTxtConfig
{
    /**
     * @var (Closure(): RobotsTxt)|null
     */
    public private(set) ?Closure $robots = null;

    public private(set) ?string $domain = null;

    /**
     * @var array<int, string>
     */
    public private(set) array $middleware = [];

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param Closure(): RobotsTxt $robots
     */
    public function robots(Closure $robots): self
    {
        $this->robots = $robots;

        return $this;
    }

    public function domain(?string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @param array<int, string> $middleware
     */
    public function middleware(array $middleware): self
    {
        $this->middleware = [
            ...$this->middleware,
            ...$middleware,
        ];

        return $this;
    }
}
