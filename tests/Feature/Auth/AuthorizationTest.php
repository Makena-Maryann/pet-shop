<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\v1\User;
use Illuminate\Http\Response;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\Jwt\Interfaces\TokenVerifier;
use App\Services\Jwt\Interfaces\TokenGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $tokenGenerator;
    protected $tokenVerifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenGenerator = $this->app->make(TokenGenerator::class);
        $this->tokenVerifier = $this->app->make(TokenVerifier::class);

        $this->withoutExceptionHandling();
    }

    public function test_user_cannot_access_protected_routes_without_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/admin/user-listing');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_user_cannot_access_protected_routes_when_user_uuid_is_missing(): void
    {
        $token = $this->tokenGenerator->generateToken(1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/admin/user-listing');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_user_cannot_access_protected_routes_when_token_belongs_to_non_existent_user(): void
    {
        $token = $this->tokenGenerator->generateToken('1');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/admin/user-listing');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_user_cannot_access_admin_routes_without_admin_role(): void
    {
        $user = User::factory()->create();

        $token = $this->tokenGenerator->generateToken($user->uuid);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/admin/user-listing');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_user_cannot_access_protected_routes_after_logout(): void
    {
        $user = User::factory()->create();

        $token = $this->tokenGenerator->generateToken($user->uuid);

        $this->actingAs($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/admin/logout');

        $response = $this->actingAs($user)->getJson('/api/v1/admin/user-listing');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_user_can_access_protected_routes_with_valid_token(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'password' => 'admin',
        ]);

        $request = LoginRequest::create('api/v1/admin/login', 'POST', [
            'email' => $user->email,
            'password' => 'admin',
        ]);

        $loginResponse = $this->postJson('api/v1/admin/login', $request->toArray());

        $responseData = json_decode($loginResponse->getContent(), true);

        $token = $responseData['data']['token'];

        $this->actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/admin/user-listing');

        $response->assertStatus(Response::HTTP_OK);
    }
}
