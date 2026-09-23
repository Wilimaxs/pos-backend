<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string  ...$permissions
    ): Response
    {
        $employee = $request->user();

        foreach ($permissions as $permission) {
            if ($employee?->hasPermission($permission)) {
                return $next($request);
            }
        }

        throw new AuthorizationException;
    }
}
