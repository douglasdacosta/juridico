<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_locked_after_ten_invalid_attempts(): void
    {
        User::create([
            'name' => 'Teste',
            'email' => 'lock@example.com',
            'password' => Hash::make('senha-correta'),
            'perfil_acesso' => 2,
        ]);

        for ($i = 0; $i < 10; $i++) {
            $this->from('/login')->post('/login', [
                'email' => 'lock@example.com',
                'password' => 'senha-incorreta',
            ]);
        }

        $response = $this->from('/login')->post('/login', [
            'email' => 'lock@example.com',
            'password' => 'senha-incorreta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'Muitas tentativas de login',
            session('errors')->first('email')
        );

        $this->assertNotNull(User::where('email', 'lock@example.com')->first()->locked_until);
    }
}
