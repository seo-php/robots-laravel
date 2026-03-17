<?php

declare(strict_types=1);

namespace Seo\Robots\Laravel\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Seo\Robots\RobotsTxt;
use Seo\Robots\RobotsTxtRenderer;

final readonly class RobotsTxtResponse implements Responsable
{
    public function __construct(private RobotsTxt $robots) {}

    public function toResponse($request): Response
    {
        return new Response(
            content: new RobotsTxtRenderer()->render($this->robots),
            status: Response::HTTP_OK,
            headers: ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }
}
