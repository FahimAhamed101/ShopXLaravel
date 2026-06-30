<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $roles = $this->normalizeArguments($roles);
        $preferWebGuard = $this->shouldPreferWebGuard($roles);

        if ($preferWebGuard) {
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                $userType = strtolower((string) ($user->user_type ?? ''));

                if (in_array($userType, $roles, true)) {
                    return $next($request);
                }
            }

            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();

                if (method_exists($admin, 'hasRole') && $admin->hasRole($roles)) {
                    return $next($request);
                }
            }

            abort(403);
        }

        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();

            if (method_exists($admin, 'hasRole') && $admin->hasRole($roles)) {
                return $next($request);
            }
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $userType = strtolower((string) ($user->user_type ?? ''));

            if (in_array($userType, $roles, true)) {
                return $next($request);
            }
        }

        abort(403);
    }

    protected function normalizeArguments(array $arguments): array
    {
        $roles = [];

        foreach ($arguments as $argument) {
            foreach (explode('|', $argument) as $role) {
                $role = strtolower(trim($role));

                if ($role !== '') {
                    $roles[] = $role;
                }
            }
        }

        return array_values(array_unique($roles));
    }

    protected function shouldPreferWebGuard(array $roles): bool
    {
        $frontendRoles = ['user', 'vendor'];

        foreach ($roles as $role) {
            if (! in_array($role, $frontendRoles, true)) {
                return false;
            }
        }

        return ! empty($roles);
    }
}
