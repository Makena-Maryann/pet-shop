<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\v1\JwtToken;
use Illuminate\Http\Request;
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
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     operationId="loginAdmin",
     *      tags={"Admin"},
     *      summary="Login an Admin account",
     *      description="Logs in an Admin account and returns a JWT token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  ref="#/components/schemas/LoginRequest"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     * )
     */
    public function loginAdmin(LoginRequest $request): JsonResponse
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
     * @OA\Post(
     *     path="/api/v1/admin/logout",
     *     operationId="logoutAdmin",
     *      tags={"Admin"},
     *      summary="Logout an Admin account",
     *      description="Logs out an Admin account and invalidates the JWT token",
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     * )
     */
    public function logoutAdmin(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token) {
            $authToken = $this->tokenVerifier->verifyToken($token);

            $uniqueId = $authToken->claims()->get('jti');

            $accessToken = JwtToken::where('unique_id', $uniqueId)->first();

            if ($accessToken) {
                $accessToken->update([
                    'expires_at' => Carbon::now(),
                ]);
            }
        }

        Auth::logout();

        return \App\Helpers\customApiResponse(true);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     operationId="loginUser",
     *      tags={"User"},
     *      summary="Login a User account",
     *      description="Logs in a User account and returns a JWT token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  ref="#/components/schemas/LoginRequest"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     * )
     */
    public function loginUser(LoginRequest $request): JsonResponse
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

    /**
     * @OA\Post(
     *     path="/api/v1/user/logout",
     *     operationId="logoutUser",
     *      tags={"User"},
     *      summary="Logout a User account",
     *      description="Logs out a User account and invalidates the JWT token",
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     * )
     */
    public function logoutUser(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token) {
            $authToken = $this->tokenVerifier->verifyToken($token);

            $uniqueId = $authToken->claims()->get('jti');

            $accessToken = JwtToken::where('unique_id', $uniqueId)->first();

            if ($accessToken) {
                $accessToken->update([
                    'expires_at' => Carbon::now(),
                ]);
            }
        }

        Auth::logout();

        return \App\Helpers\customApiResponse(true);
    }
}
