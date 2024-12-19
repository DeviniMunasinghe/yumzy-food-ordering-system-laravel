<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log; 

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        Log::info('RoleMiddleware invoked with roles:', $roles);

        $user = $request->user();
        Log::info('User Details', ['user' => $user]);

        if (!$user || !in_array($user->role, $roles)) {
            Log::warning('Unauthorized access attempt', ['user' => $user, 'roles' => $roles]);
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
