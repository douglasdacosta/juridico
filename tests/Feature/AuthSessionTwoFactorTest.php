<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthSessionTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_lifetime_is_configured_to_24_hours(): void
    {
        $this->assertSame(1440, (int) config('session.lifetime'));
    }

    public function test_user_can_enable_and_disable_optional_two_factor(): void
    {
        $user = User::create([
            'name' => 'Usuário 2FA',
            'email' => 'user2fa@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 2,
        ]);

        $this->actingAs($user)
            ->post('/settings', [
                'nome' => 'Usuário 2FA',
                'email' => 'user2fa@example.com',
                'password' => '',
                'two_factor_enabled' => '1',
            ])
            ->assertRedirect('/settings');

        $user->refresh();
        $this->assertTrue((bool) $user->two_factor_enabled);
        $this->assertNotEmpty($user->two_factor_secret);

        $this->actingAs($user)
            ->post('/settings', [
                'nome' => 'Usuário 2FA',
                'email' => 'user2fa@example.com',
                'password' => '',
            ])
            ->assertRedirect('/settings');

        $user->refresh();
        $this->assertFalse((bool) $user->two_factor_enabled);
        $this->assertNull($user->two_factor_secret);
    }
}
