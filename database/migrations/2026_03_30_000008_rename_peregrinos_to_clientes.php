<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('peregrinos') && ! Schema::hasTable('clientes')) {
            Schema::rename('peregrinos', 'clientes');
        }

        if (Schema::hasTable('submenus')) {
            DB::table('submenus')
                ->where('rota', 'peregrinos')
                ->update([
                    'rota' => 'clientes',
                    'nome' => 'Clientes',
                ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('clientes') && ! Schema::hasTable('peregrinos')) {
            Schema::rename('clientes', 'peregrinos');
        }

        if (Schema::hasTable('submenus')) {
            DB::table('submenus')
                ->where('rota', 'clientes')
                ->update([
                    'rota' => 'peregrinos',
                    'nome' => 'Conta',
                ]);
        }
    }
};
