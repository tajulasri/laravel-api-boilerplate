<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $authToken;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->authToken = \JWTAuth::fromUser($this->user);
    }

    public function test_can_login()
    {
        $response = $this->json('post', 'api/auth/login', [
            'email' => $this->user->email,
            'password' => 'secret'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'data' => $this->user->transform(),
            'token' => $this->authToken,
        ]);
    }

    public function test_cannot_login_with_invalid_credentials()
    {
        $response = $this->json('post', 'api/auth/login', [
            'email' => $this->user->email,
            'password' => 'nosecret'
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'message' => 'Invalid credentials!',
        ]);
    }

    public function test_can_register_an_account()
    {
        $user = factory(User::class)->make();

        $response = $this->json('post', 'api/auth/register', [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $createdUser = User::whereEmail($user->email)->first();

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'data' => $createdUser->transform(),
            'token' => \JWTAuth::fromUser($createdUser),
        ]);
    }

    public function test_cannot_register_if_email_already_taken()
    {
        $response = $this->json('post', 'api/auth/register', [
            'name' => $this->user->name,
            'username' => 'testusername',
            'email' => $this->user->email,
            'password' => 'secret'
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'Validation failed!',
            'errors' => [
                'email' => ['The email has already been taken.']
            ]
        ]);
    }

    public function test_can_refresh_auth_token()
    {
        $response = $this->json('get', 'api/auth/refresh', [], [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'token' => \JWTAuth::fromUser($this->user),
        ]);
    }

    public function test_can_get_current_user_data_when_authenticated()
    {
        $response = $this->json('get', 'api/auth/user', [], [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'data' => $this->user->transform(),
        ]);
    }
}
