<?php

namespace Tests\Feature;

use App\Exports\ClientesExport;
use App\Exports\FinanceiroExport;
use App\Exports\ProcessosExport;
use App\Models\Cliente;
use App\Models\Financeiro;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportXlsxTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::create([
            'name' => 'Admin Excel',
            'email' => 'excel@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    public function test_authenticated_user_can_export_processos_xlsx(): void
    {
        Excel::fake();

        $user = $this->actingUser();

        Processo::create([
            'numero_processo' => '000900-11.2026.8.26.0001',
            'vara_tribunal' => '9ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);

        $this->actingAs($user)->get('/exportar-processos-xlsx')->assertOk();

        Excel::assertDownloaded('processos.xlsx', function (ProcessosExport $export) {
            return $export->collection()->count() === 1;
        });
    }

    public function test_authenticated_user_can_export_clientes_xlsx(): void
    {
        Excel::fake();

        $user = $this->actingUser();

        Cliente::create([
            'nome' => 'Cliente Excel',
            'email' => 'cliente.excel@example.com',
            'status' => 'A',
        ]);

        $this->actingAs($user)->get('/exportar-clientes-xlsx')->assertOk();

        Excel::assertDownloaded('clientes.xlsx', function (ClientesExport $export) {
            return $export->collection()->count() === 1;
        });
    }

    public function test_authenticated_user_can_export_financeiro_xlsx(): void
    {
        Excel::fake();

        $user = $this->actingUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Financeiro Excel',
            'email' => 'cliente.financeiro.excel@example.com',
            'status' => 'A',
        ]);

        Financeiro::create([
            'cliente_id' => $cliente->id,
            'valor_causa' => 500,
            'parcelado' => false,
        ]);

        $this->actingAs($user)->get('/exportar-financeiro-xlsx')->assertOk();

        Excel::assertDownloaded('financeiro.xlsx', function (FinanceiroExport $export) {
            return $export->collection()->count() === 1;
        });
    }
}
