<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_register(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => 'password',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        $dataFragment = [
            'id'=>1,
            'email' => 'email@email.com',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        $response = $this->postJson("{$this->apiBase}/users", $data);

        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment(['message'=>'ok', 'data' => ['user' => $dataFragment], 'status'=>200]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'email@email.com',
            'name' => 'example',
            'last_name' => 'example example',
        ]);
    }

    #[Test]
    public function a_registered_user_can_login(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => 'password',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        $this->postJson("{$this->apiBase}/users", $data);

        $dataLogin = ['email' => 'email@email.com', 'password' => 'password'];
        $response = $this->postJson("{$this->apiBase}/login", $dataLogin);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function email_must_be_required(): void
    {
        //$this->withoutExceptionHandling();
        #teniendo
        $data = [
            'email' => '',
            'password' => 'password',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        //$this->withoutExceptionHandling();
        #teniendo
        $data = [
            'email' => 'aaaaaa',
            'password' => 'password',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_unique(): void
    {
        //$this->withoutExceptionHandling();
        User::factory()->create(['email' => 'email@email.com']);

        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => 'password',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => '',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_have_at_lease_8_cahracters(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => 'pass',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function name_must_be_required(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => '12345678',
            'name' => '',
            'last_name' => 'example example',
        ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    #[Test]
    public function name_must_have_at_lease_2_cahracters(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => 'pass123456',
            'name' => 'e',
            'last_name' => 'example example',
        ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    #[Test]
    public function last_name_must_be_required(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => '12345678',
            'name' => 'example',
            'last_name' => '',
        ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

    #[Test]
    public function last_name_must_have_at_lease_2_cahracters(): void
    {
        #teniendo
        $data = [
            'email' => 'email@email.com',
            'password' => 'pass123456',
            'name' => 'example',
            'last_name' => 'q',
        ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

}
