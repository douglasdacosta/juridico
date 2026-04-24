<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_post_generates_audit_log_with_masked_password(): void
    {
        $user = User::create([
            'name' => 'Auditoria',
            'email' => 'audit@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $this->actingAs($user)
            ->post('/settings', [
                'nome' => 'Auditoria Atualizada',
                'email' => 'audit@example.com',
                'password' => 'nova-senha-segura',
                'status' => 'A',
            ])
            ->assertRedirect('/settings');

        $log = AuditLog::query()->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertSame($user->id, (int) $log->user_id);
        $this->assertSame('settings', $log->entity);
        $this->assertSame('create', $log->action);
        $this->assertSame('settings', $log->uri);
        $this->assertSame('***', data_get($log->after, 'request.password'));
    }
}
