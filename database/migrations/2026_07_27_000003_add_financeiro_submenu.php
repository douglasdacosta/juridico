<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('submenus')) {
            return;
        }

        // Menu "Controles" (id 2), mesmo grupo de Processos/Andamentos/Documentos
        DB::table('submenus')->insertOrIgnore([
            'menu_id' => 2,
            'nome' => 'Financeiro',
            'rota' => 'financeiro',
            'icon' => 'fa fa-fw fa-angle-right',
            'icon_color' => 'grey',
            'ordem' => 0,
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('submenus')) {
            return;
        }

        DB::table('submenus')
            ->where('rota', 'financeiro')
            ->delete();
    }
};
