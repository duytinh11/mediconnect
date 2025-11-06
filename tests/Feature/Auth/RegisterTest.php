<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_profile(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0900000000',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'address' => '123 Main Street',
            'gender' => 'female',
            'dob' => '1995-08-15',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertCreated()
                 ->assertJsonPath('user.email', 'jane@example.com');

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
        $this->assertDatabaseHas('patients', [
            'user_id' => $response->json('user.id'),
            'address' => '123 Main Street',
        ]);
    }
}
