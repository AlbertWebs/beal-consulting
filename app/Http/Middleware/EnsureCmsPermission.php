<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCmsPermission
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        abort_unless($request->user()?->canManageCms($module), 403);

        return $next($request);
    }
}
