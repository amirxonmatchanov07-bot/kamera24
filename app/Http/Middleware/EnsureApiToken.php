<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $user = $token ? User::where('api_token', $token)->first() : null;

        if (! $user) {
            return response()->json(['message' => "Token noto'g'ri yoki yo'q."], 401);
        }

        if (! $user->isAdmin()) {
            return response()->json(['message' => "Sizda ushbu amalni bajarish huquqi yo'q."], 403);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
