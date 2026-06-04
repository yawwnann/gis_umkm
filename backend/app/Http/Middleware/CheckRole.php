<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $allowedRoles = array_map(fn($role) => UserRole::from($role), $roles);

        if (!in_array($user->role, $allowedRoles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
