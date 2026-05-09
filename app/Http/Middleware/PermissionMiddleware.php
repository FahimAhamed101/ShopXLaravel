<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $permissions = $this->normalizeArguments($permissions);

        if (! Auth::guard('admin')->check()) {
            abort(403);
        }

        $admin = Auth::guard('admin')->user();

        if (method_exists($admin, 'hasAnyPermission') && $admin->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403);
    }

    protected function normalizeArguments(array $arguments): array
    {
        $permissions = [];

        foreach ($arguments as $argument) {
            foreach (explode('|', $argument) as $permission) {
                $permission = trim($permission);

                if ($permission !== '') {
                    $permissions[] = $permission;
                }
            }
        }

        return array_values(array_unique($permissions));
    }
}
