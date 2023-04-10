<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\v1\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Jwt\Interfaces\TokenVerifier;
use App\Services\Jwt\Interfaces\TokenGenerator;

class AuthenticatedSessionController extends Controller
{
    protected $tokenGenerator;
    protected $tokenVerifier;

    public function __construct(TokenGenerator $tokenGenerator, TokenVerifier $tokenVerifier)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenVerifier = $tokenVerifier;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = $request->user();
        $token = $this->tokenGenerator->generateToken($user->uuid);

        $unencryptedToken = $this->tokenVerifier->verifyToken($token);

        try {
            $this->createOrUpdateJwtToken($user, $unencryptedToken);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()], 500);
        }

        return \App\Helpers\customApiResponse(true, ['token' => $token]);
    }

    private function createOrUpdateJwtToken($user, $unencryptedToken)
    {
        $uniqueId = $unencryptedToken->claims()->get('jti');
        $expiry = $unencryptedToken->claims()->get('exp');

        JwtToken::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'unique_id' => $uniqueId,
            'token_title' => 'Access Token',
            'expires_at' => $expiry,
            'last_used_at' => now(),
            'refreshed_at' => now(),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $authToken = $this->tokenVerifier->verifyToken($token);
        $uniqueId = $authToken->claims()->get('jti');

        $accessToken = JwtToken::where('unique_id', $uniqueId)->first();

        if ($accessToken) {
            $accessToken->update([
                'expires_at' => Carbon::now(),
            ]);
        }

        Auth::logout();

        return response()->json([
            'success' => 1,
            'data' => []
        ], Response::HTTP_OK);
    }
}
