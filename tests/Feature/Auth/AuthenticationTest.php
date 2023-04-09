<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\v1\User;
use App\Models\v1\JwtToken;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Jwt\Interfaces\TokenVerifier;
use App\Services\Jwt\Interfaces\TokenGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $tokenGenerator;
    protected $tokenVerifier;

    public function setUp(): void
    {
        parent::setUp();

        $this->tokenGenerator = $this->app->make(TokenGenerator::class);
        $this->tokenVerifier = $this->app->make(TokenVerifier::class);
    }

    public function test_users_cannot_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $request = LoginRequest::create('api/v1/admin/login', 'POST', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response = $this->postJson('api/v1/admin/login', $request->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }

    public function test_user_password_remains_the_same_for_seeded_users()
    {
        $users = User::factory()->count(2)->create();

        $users->each(function ($user) {
            $this->assertTrue(Hash::check('userpassword', $user->password));
        });
    }

    public function test_can_authenticate_a_user_and_return_a_token()
    {
        $user = User::factory()->create();

        $request = LoginRequest::create('api/v1/admin/login', 'POST', [
            'email' => $user->email,
            'password' => 'userpassword',
        ]);

        $response = $this->postJson('api/v1/admin/login', $request->toArray());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => ['token'],
            ]);
    }

    public function test_generated_token_unique_id_is_stored_in_jwt_tokens_table()
    {
        $user = User::factory()->create();

        $request = LoginRequest::create('api/v1/admin/login', 'POST', [
            'email' => $user->email,
            'password' => 'userpassword',
        ]);

        $response = $this->postJson('api/v1/admin/login', $request->toArray());

        $responseData = json_decode($response->getContent(), true);

        $token = $responseData['data']['token'];
        $unencryptedToken = $this->tokenVerifier->verifyToken($token);

        $jwtToken = JwtToken::where('unique_id', $unencryptedToken->claims()->get('jti'))
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($jwtToken);
    }

    public function test_can_destroy_an_authenticated_session()
    {
        $user = User::factory()->create();

        $token = $this->tokenGenerator->generateToken($user->uuid);

        $this->actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/admin/logout');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ]);
    }
}
