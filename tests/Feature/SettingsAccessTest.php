<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_settings(): void
    {
        $this->get('/settings')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_settings(): void
    {
        $user = User::create([
            'name' => 'Usuário',
            'email' => 'settings@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 2,
        ]);

        $this->actingAs($user)
            ->get('/settings')
            ->assertStatus(200);
    }
}
