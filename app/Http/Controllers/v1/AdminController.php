<?php

namespace App\Http\Controllers\v1;

use App\Models\v1\User;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminController extends Controller
{
    public function login()
    {
        //login an admin account
    }

    public function logout()
    {
        //logout an admin account
    }

    /**
     * Display a listing of the resource.
     */
    public function userListing()
    {
        $users = User::where('is_admin', false)->paginate(5)->toArray();

        return response()->json([
            $users,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // Create a new admin account
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
