<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Seo\Robots\Laravel\Http\Controllers\RobotsTxtGetController;
use Seo\Robots\Laravel\RobotsTxtConfigRegistry;

foreach (app(RobotsTxtConfigRegistry::class)->all() as $domain => $config) {
    Route::domain($domain)
        ->middleware($config->middleware)
        ->get('/robots.txt', RobotsTxtGetController::class)
    ;
}
