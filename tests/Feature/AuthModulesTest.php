<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'reset@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'reset@example.com',
        ]);

        $response->assertRedirect();
        Notification::assertSentTo(
            User::where('email', 'reset@example.com')->first(),
            ResetPassword::class
        );
    }

    public function test_user_can_update_profile_and_change_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'profile@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $this->post('/profile', [
            'name' => 'New Name',
            'email' => 'profile@example.com',
        ])->assertRedirect();

        $this->post('/profile/password', [
            'current_password' => 'password',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect();

        $user->refresh();

        $this->assertEquals('New Name', $user->name);
        $this->assertTrue(Hash::check('NewPassword123', $user->password));
    }
}
