<?php

namespace Tests\Feature\Profile;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile_and_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Patient::factory()->for($user)->create([
            'address' => 'Old Address',
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Updated Name',
            'phone' => '0911111111',
            'address' => 'New Address',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 300, 300),
        ];

        $response = $this->post('/api/profile', array_merge($payload, ['_method' => 'PUT']));

        $response->assertOk()
                 ->assertJsonPath('user.name', 'Updated Name');

        $user = $user->fresh();
        $this->assertDatabaseHas('patients', [
            'user_id' => $user->id,
            'address' => 'New Address',
        ]);

        Storage::disk('public')->assertExists($user->avatar_path);
    }
}
