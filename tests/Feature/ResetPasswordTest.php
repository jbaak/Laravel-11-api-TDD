<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\userSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;
    protected $token;
    protected $email;

    /**
     * @return array
     */
    public function resetPassword()
    {
//$this->withoutExceptionHandling();
        Notification::fake();
        #teniendo
        $credentials = ['email' => 'example@example.com'];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $credentials);

        #esperando
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'OK']);
        $user = User::find(1);

        Notification::assertSentTo([$user], function (ResetPasswordNotification $notification) {
            $url = $notification->url;
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $this->token = $query['token'];
            $this->email = $query['email'];
            return str_contains($url, 'http://front.app/reset-password?token=');
        });
    }

    protected function setUp(): void{
        parent::setUp();
        $this->seed(userSeeder::class);

    }

    #[Test]
    public function an_existing_user_can_reset_their_password(): void
    {
        $this->resetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
            'email' => $this->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $user = User::find(1);
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors']);
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    #[Test]
    public function email_must_be_required(): void
    {
        #teniendo
        $credentials = [ 'email' => '' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        $credentials = [ 'email' => 'notemail' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_an_exisiting_email(): void
    {
        #teniendo
        $credentials = [ 'email' => 'notexists@nonexist.com' ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        $this->resetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
            'email' => $this->email,
            'password' => '',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' =>['password']]);
    }

    #[Test]
    public function password_must_be_confimed(): void
    {
        $this->resetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
            'email' => $this->email,
            'password' => 'newpassword',
            'password_confirmation' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' =>['password']]);
    }

    #[Test]
    public function token_must_be_a_valid_token(): void
    {
        $this->resetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}dfsdfsdfs", [
            'email' => $this->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'data', 'status']);
        $response->assertJsonFragment([
            'message' => 'Invalid token',
        ]);
    }

    #[Test]
    public function email_must_be_associated_with_the_token(): void
    {
        $this->resetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
            'email' => 'fake@gmail.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'data', 'status']);
        $response->assertJsonFragment([
            'message' => 'Invalid email',
        ]);
    }

}
