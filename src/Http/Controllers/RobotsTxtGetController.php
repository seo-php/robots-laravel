<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel\Http\Controllers;

use Seo\Robots\Laravel\Http\Responses\RobotsTxtResponse;
use Seo\Robots\RobotsTxt;

final readonly class RobotsTxtGetController
{
    public function __construct(private RobotsTxt $robots) {}

    public function __invoke(): RobotsTxtResponse
    {
        return new RobotsTxtResponse($this->robots);
    }
}
