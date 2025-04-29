<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // إذا لم يكن الطلب API (لا يطلب JSON)، نرجع رسالة 401 بدون توجيه
        if (!$request->expectsJson()) {
            abort(401, 'Unauthorized');
        }

        return null; // في حال الطلب يتوقع JSON، لا توجيه
    }
}
