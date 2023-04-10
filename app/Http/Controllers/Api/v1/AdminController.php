<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\User;
use App\Filters\UserFilters;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function userListing(Request $request): JsonResponse
    {
        $users = User::where('is_admin', false)
            ->filterBy($request->all());

        return response()->json([
            $this->parseQuery($users, $request),
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
