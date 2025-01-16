<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|string[]  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        $user = Auth::user();

    if (!$user || !in_array($user->roles->pluck('name')->first(), $roles)) {
        abort(403, 'Unauthorized access');
    }

    return $next($request);
    }
}
