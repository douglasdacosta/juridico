<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        // Encontrar o menu pai apropriado (geralmente Configurações ou Administração)
        $menu_pai_id = DB::table('menus')
            ->where('nome', 'like', '%Configura%')
            ->orWhere('nome', 'like', '%Admin%')
            ->first()?->id ?? 1;

        // Se não encontrar, usar o menu_id 1 como padrão
        if (!$menu_pai_id) {
            $menu_pai_id = 1;
        }

        // Inserir o submenu "Tipos de Ação"
        DB::table('submenus')->insertOrIgnore([
            'menu_id' => $menu_pai_id,
            'nome' => 'Tipos de Ação',
            'rota' => 'tipos-acao',
            'icon' => 'fa fa-fw fa-angle-right',
            'icon_color' => 'grey',
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
        DB::table('submenus')
            ->where('rota', 'tipos-acao')
            ->delete();
    }
};
