<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\userSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void{
        parent::setUp();
        $this->seed(userSeeder::class);
    }


    #[Test]
    public function an_authenticated_user_can_update_their_password(): void
    {
        #teniendo
        $data = [
            'old_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/password", $data);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $user = User::find(1);

        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    #[Test]
    public function old_password_must_be_validated(): void
    {
        #teniendo
        $data = [
            'old_password' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/password", $data);

        $response->assertStatus(422);

        $response->assertJsonStructure(['message', 'data', 'errors' => ['old_password'], 'status']);
    }

    #[Test]
    public function old_password_must_be_required(): void
    {
        #teniendo
        $data = [
            'old_password' => '',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/password", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['old_password']]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        #teniendo
        $data = [
            'old_password' => 'password',
            'password' => '',
            'password_confirmation' => 'newpassword',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/password", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_be_confirmed(): void
    {
        #teniendo
        $data = [
            'old_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => '',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/password", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }
}
