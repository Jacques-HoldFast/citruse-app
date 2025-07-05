<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userRole = $request->user()->role;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Insufficient permissions. Required roles: ' . implode(', ', $roles),
                'your_role' => $userRole
            ], 403);
        }

        return $next($request);
    }
}