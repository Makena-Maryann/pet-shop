<?php

namespace App\Http\Controllers\Auth;

use App\Models\v1\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Jwt\Interfaces\TokenGenerator;


class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, TokenGenerator $tokenGenerator): JsonResponse
    {
        $request->authenticate();

        $user = $request->user();
        $uniqueId = bin2hex(random_bytes(8));
        $token = $tokenGenerator->generateToken($user->uuid, $uniqueId);

        try {
            $this->createOrUpdateJwtToken($user, $uniqueId);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => 1, 'token' => $token]);
    }

    private function createOrUpdateJwtToken($user, $uniqueId)
    {
        JwtToken::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'unique_id' => $uniqueId,
            'token_title' => 'Auth Token',
            'expires_at' => now()->addMinutes(config('jwt.expires_at')),
            'last_used_at' => now(),
            'refreshed_at' => now(),
        ]);
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
