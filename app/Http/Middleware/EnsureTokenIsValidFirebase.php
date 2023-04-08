<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\v1\User;
use App\Models\v1\JwtToken;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValidFirebase
{
    private $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
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

        // $jwtToken = JwtToken::where('unique_id', $token)->first();

        // if (!$jwtToken || $jwtToken->isExpired()) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        // $userUuid = $this->tokenService->verifyToken($jwtToken);
        $userUuid = $this->tokenService->verifyToken($token);

        if (!$userUuid) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->setUserResolver(function () use ($userUuid) {
            return User::where('uuid', $userUuid)->first();
        });

        //if user is admin
        // if ($guard == 'admin' && !$request->user()->is_admin) {
        //    return response()->json(['message' => 'Unauthorized'], 401);
        // }

        //if user is user
        // if ($guard == 'user' && $request->user()->is_admin) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        return $next($request);

        //with $guard
        // $payload = $jwt->decode($token);
        // $user_type = $payload['user_type'];

        // if ($guard == 'admin' && $user_type != 'admin') {
        //     return response()->json([
        //         'message' => 'Unauthorized'
        //     ], 403);
        // }

        // if ($guard == 'user' && $user_type != 'user') {
        //     return response()->json([
        //         'message' => 'Unauthorized'
        //     ], 403);
        // }

        //with Guards
        // $token = $request->bearerToken();

        // if (!$token) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        // try {
        //     $jwt = (new Parser())->parse((string) $token);
        //     $signer = new Sha256();
        //     $publicKey = file_get_contents(storage_path('app/public.pem'));

        //     if (!$jwt->verify($signer, $publicKey)) {
        //         return response()->json(['error' => 'Unauthorized'], 401);
        //     }

        //     $validationData = new ValidationData();
        //     $validationData->setIssuer(request()->getHost());

        //     if (!$jwt->validate($validationData)) {
        //         return response()->json(['error' => 'Unauthorized'], 401);
        //     }

        //     $claims = $jwt->getClaims();

        //     if (!$claims->has('user_uuid') || !$claims->has('user_type')) {
        //         return response()->json(['error' => 'Unauthorized'], 401);
        //     }

        //     $userType = $claims->get('user_type');

        //     if (!in_array($userType, $guards)) {
        //         return response()->json(['error' => 'Unauthorized'], 401);
        //     }

        //     $request->merge(['user_uuid' => $claims->get('user_uuid')]);

        //     return $next($request);
        // } catch (Exception $e) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
    }
}
