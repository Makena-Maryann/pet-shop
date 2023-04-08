<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\User;
use App\Models\v1\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Signer\Key\InMemory;
use App\Services\TokenServiceLcobucci;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokenService = new TokenService(
            config('app.url'),
            InMemory::file(__DIR__ . '/../../private.pem'),
            InMemory::file(__DIR__ . '/../../public.pem')
        );

        $user = $request->user();
        $token = $tokenService->generateToken($user->uuid);

        JwtToken::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'unique_id' => $token,
            'token_title' => 'Admin Token',
            'expires_at' => now()->addHours(2),
            'last_used_at' => now(),
            'refreshed_at' => now(),
        ]);

        return response()->json(['success' => 1, 'token' => $token]);
    }

    public function logout()
    {
        //logout an admin account
        //TODO: set token to expired in the database
    }

    /**
     * Display a listing of the resource.
     */
    public function userListing(): JsonResponse
    {
        //TODO: All listing endpoints most include a paginated response and include these basic filters:Page, limit, sort by, desc
        $users = User::where('is_admin', false)->paginate(5)->toArray();

        return response()->json([
            $users,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create(array_merge($request->validated(), ['is_admin' => true]));

        return response()->json([
            'success' => 1,
            //add token
            'data' => new UserResource($user),
            'message' => 'User created successfully',
            'error' => '',
            'errors' => [],
            'trace' => '',
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function userEdit(UpdateUserRequest $request, User $user)
    {
        //Edit user’s accounts
        //Admins accounts cannot be edited
    }

    /**
     * Remove the specified resource from storage.
     */
    public function userDelete(User $user)
    {
        //Delete user’s accounts
        //Admins accounts cannot be deleted
    }
}
