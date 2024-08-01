<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\userSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void{
        parent::setUp();
        $this->seed(userSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_can_modify_their_data(): void
    {
        #teniendo
        $data = [
            'name' => 'newname',
            'last_name' => 'new lastname',
        ];

        $dataFragment = [
            'id'=>1,
            'email' => 'example@example.com',
            'name' => 'newname',
            'last_name' => 'new lastname',
        ];

        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment(['message'=>'ok', 'data' => ['user' => $dataFragment], 'status'=>200]);

        $this->assertDatabaseMissing('users', [
            'email' => 'example@example.com',
            'name' => 'User',
            'last_name' => 'Test',
        ]);
    }

    #[Test]
    public function an_authenticated_user_cannot_modify_their_email(): void
    {
        #teniendo
        $data = [
            'email' => 'example@example.com',
            'name' => 'newname',
            'last_name' => 'new lastname',
        ];

        $dataFragment = [
            'id'=>1,
            'email' => 'example@example.com',
            'name' => 'newname',
            'last_name' => 'new lastname',
        ];

        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment(['message'=>'ok', 'data' => ['user' => $dataFragment], 'status'=>200]);
    }

    #[Test]
    public function an_authenticated_user_cannot_modify_their_password(): void
    {
        #teniendo
        $data = [
            'password' => 'newpassword',
            'name' => 'newname',
            'last_name' => 'new lastname',
        ];

        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
        $response->assertStatus(200);
        $user = User::find(1);
        $this->assertFalse(Hash::check('newpassword', $user->password));

    }

    #[Test]
    public function name_must_be_required(): void
    {
        #teniendo
        $data = [
            'name' => '',
            'last_name' => 'example example',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    #[Test]
    public function name_must_have_at_lease_2_cahracters(): void
    {
        #teniendo
        $data = [
            'name' => 'e',
            'last_name' => 'example example',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    #[Test]
    public function last_name_must_be_required(): void
    {
        #teniendo
        $data = [
            'name' => 'example',
            'last_name' => '',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

    #[Test]
    public function last_name_must_have_at_lease_2_cahracters(): void
    {
        #teniendo
        $data = [
            'name' => 'example',
            'last_name' => 'q',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }
}
