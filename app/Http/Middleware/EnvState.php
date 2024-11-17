<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnvState
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next,
        mixed $envs = null,
        string|int|null $statusOnFail = 404,
    ): Response {
        if (!$envs || !(is_string($envs) || is_array($envs))) {
            return $next($request);
        }

        $envs = collect(is_string($envs) ? explode('|', $envs) : $envs)
            ->filter(fn ($item) => is_string($item) && trim($item))
            ->map(fn ($item) => trim($item))
            ->toArray();

        $statusOnFail = filter_var($statusOnFail, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 404;

        abort_unless($envs && app()->environment($envs), $statusOnFail);

        return $next($request);
    }
}
