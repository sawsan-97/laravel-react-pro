<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Middleware\Cors;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // أي Middleware عام
    ];

    protected $middlewareGroups = [
        'api' => [
            EnsureFrontendRequestsAreStateful::class,
            Cors::class, // هنا ضفناه
            'throttle:api',
            SubstituteBindings::class,
        ],
        'web' => [],
    ];
}
