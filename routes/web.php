<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Seo\Robots\Laravel\Http\Controllers;

Route::get('/robots.txt', Controllers\RobotsTxtGetController::class);
