<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\v1\User;
use Illuminate\Http\Request;
use App\Services\Jwt\Interfaces\TokenVerifier;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    private $tokenVerifier;

    public function __construct(TokenVerifier $tokenVerifier)
    {
        $this->tokenVerifier = $tokenVerifier;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userUuid = $this->tokenVerifier->verifyToken($token);

        if (!$userUuid) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('uuid', $userUuid)->first();

        if ($guard == 'admin' && !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized: Not enough privileges'], 401);
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
