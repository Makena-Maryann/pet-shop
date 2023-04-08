<?php

namespace App\Http\Controllers\Auth;

use App\Models\v1\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Signer\Key\InMemory;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest  $request): JsonResponse
    {
        $request->authenticate();

        $user = $request->user();
        $expiresAt = now()->addHours(2);
        $now = now();
        $uniqueId = bin2hex(random_bytes(8));

        $tokenService = new TokenService(
            config('app.url'),
            InMemory::file(__DIR__ . '/../../../../private.pem'),
            InMemory::file(__DIR__ . '/../../../../public.pem'),
            $uniqueId
        );

        $token = $tokenService->generateToken($user->uuid);

        JwtToken::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'unique_id' => $uniqueId,
            'token_title' => 'Auth Token',
            'expires_at' => $expiresAt,
            'last_used_at' => $now,
            'refreshed_at' => $now,
        ]);

        return response()->json(['success' => 1, 'token' => $token]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        //TODO: set token to expired in the database

        return response()->noContent();
    }
}
