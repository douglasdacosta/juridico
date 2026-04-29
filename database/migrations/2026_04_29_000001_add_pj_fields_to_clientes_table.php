<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'tipo_pessoa')) {
                $table->char('tipo_pessoa', 1)->default('F')->after('nome')
                    ->comment('F=Pessoa Física, J=Pessoa Jurídica');
            }
            if (! Schema::hasColumn('clientes', 'cnpj')) {
                $table->string('cnpj', 18)->nullable()->after('cpf')
                    ->comment('CNPJ para pessoa jurídica');
            }
            if (! Schema::hasColumn('clientes', 'socios')) {
                $table->json('socios')->nullable()->after('cnpj')
                    ->comment('JSON: [{nome, cpf, endereco}, ...]');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['tipo_pessoa', 'cnpj', 'socios']);
        });
    }
};
