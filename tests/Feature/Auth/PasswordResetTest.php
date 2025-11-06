<?php

namespace Tests\Feature\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use App\Models\User;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->postJson('/api/password/forgot', ['email' => $user->email]);

        $response->assertOk();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertOk();
        $this->assertTrue(\Hash::check('NewPassword123!', $user->fresh()->password));
    }
}
