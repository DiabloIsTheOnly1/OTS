<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AccessPermission
{
    public function handle($request, Closure $next, $permission)
    {
        $user = auth()->user();

        if (!$user || !$user->accessLevel || !$user->canAccess($permission)) {
            abort(403, 'Access denied: ' . $permission);
        }

        return $next($request);
    }
}