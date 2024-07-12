<?php

namespace Tests\Feature;

use Database\Seeders\userSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void{
        parent::setUp();
        $this->seed(userSeeder::class);
    }

    #[Test]
    public function a_user_can_login(): void
    {
        //$this->withoutExceptionHandling();
        #teniendo
        $credentials = [ 'email' => 'example@example.com', 'password' => 'password' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function a_non_existing_user_cannot_login(): void
    {
        #teniendo
        $credentials = [ 'email' => 'example@nonexist.com', 'password' => 'qwe45tyy' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(401);
        $response->assertJsonFragment(['status' => 401, 'message' => 'Unauthorized']);
    }

    #[Test]
    public function email_must_be_required(): void
    {
        #teniendo
        $credentials = [ 'password' => '12345678' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        #teniendo
        $credentials = [ 'email' => 'asdasdasda', 'password' => '12345678' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        #teniendo
        $credentials = [ 'email' => 'example@nonexist.com' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_have_at_lease_8_cahracters(): void
    {
        #teniendo
        $credentials = [ 'email' => 'example@nonexist.com', 'password' => 'abc' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }
}
