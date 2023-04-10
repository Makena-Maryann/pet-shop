<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\User;
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
     * @OA\Get(
     *      path="/api/v1/admin/user-listing",
     *      operationId="getUsersList",
     *      tags={"Admin"},
     *      summary="List all users",
     *      description="Returns all non-admins users",
     *     @OA\Parameter(
     *          name="page",
     *          parameter="page",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *          name="limit",
     *          parameter="limit",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *          name="sortBy",
     *          parameter="sortBy",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *          name="desc",
     *          parameter="desc",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *     ),
     *     @OA\Parameter(
     *          name="first_name",
     *          parameter="first_name",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *          name="email",
     *          parameter="email",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *          name="phone",
     *          parameter="phone",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *          name="address",
     *          parameter="address",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *          name="created_at",
     *          parameter="created_at",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *          name="marketing",
     *          parameter="marketing",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
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
     *      security={
     *         {"bearerAuth": {}}
     *    }
     * )
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
     * @OA\Post(
     *      path="/api/v1/admin/create",
     *      operationId="storeAdmin",
     *      tags={"Admin"},
     *      summary="Create an Admin account",
     *      description="Create a new admin account",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  ref="#/components/schemas/StoreUserRequest"
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
     *      security={
     *         {"bearerAuth": {}}
     *    }
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create(array_merge($request->validated(), ['is_admin' => true]));

        return \App\Helpers\customApiResponse(true, ['user' => $user]);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/user-edit/{user}",
     *      operationId="updateUser",
     *      tags={"Admin"},
     *      summary="Edit a User account",
     *      description="Edits user’s account",
     *      @OA\Parameter(
     *          name="user",
     *          description="User UUID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  ref="#/components/schemas/StoreUserRequest"
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
     *      security={
     *         {"bearerAuth": {}}
     *    }
     * )
     */
    public function userEdit(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return \App\Helpers\customApiResponse(true, ['user' => $user]);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/user-delete/{user}",
     *      operationId="deleteUser",
     *      tags={"Admin"},
     *      summary="Delete a User account",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="user",
     *          description="User UUID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *      ),
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
     *      security={
     *         {"bearerAuth": {}}
     *    }
     * )
     */
    public function userDelete(User $user)
    {
        $user->delete();

        return \App\Helpers\customApiResponse(true);
    }
}
