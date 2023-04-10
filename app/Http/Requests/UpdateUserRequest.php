<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="UpdateUserRequest",
 *    required={"first_name","last_name","email","password","password_confirmation","address","phone_number"},
 *    @OA\Property(
 *     property="first_name",
 *     type="string",
 *     description="User first name",
 *    ),
 *     @OA\Property(
 *     property="last_name",
 *     type="string",
 *     description="User last name",
 *    ),
 *     @OA\Property(
 *     property="email",
 *     type="string",
 *     description="User email",
 *    ),
 *    @OA\Property(
 *     property="password",
 *     type="string",
 *     format="password",
 *     description="User password",
 *    ),
 *     @OA\Property(
 *     property="password_confirmation",
 *     type="string",
 *     format="password",
 *     description="User password",
 *    ),
 *     @OA\Property(
 *     property="avatar",
 *     type="string",
 *     description="Avatar image UUID",
 *    ),
 *     @OA\Property(
 *     property="address",
 *     type="string",
 *     description="User main address",
 *    ),
 *     @OA\Property(
 *     property="phone_number",
 *     type="string",
 *     description="User main phone number",
 *    ),
 *     @OA\Property(
 *     property="is_marketing",
 *     type="boolean",
 *     description="User marketing preferences",
 *    ),
 * )
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|confirmed',
            'avatar' => 'nullable',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:255',
            'is_marketing' => 'nullable|boolean',
        ];
    }
}
