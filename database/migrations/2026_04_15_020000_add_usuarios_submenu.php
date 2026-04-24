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
        // Find the Configurações menu (usually menu_id = 3)
        $menu_pai_id = DB::table('menus')
            ->where('nome', 'like', '%Configura%')
            ->orWhere('nome', 'like', '%Admin%')
            ->first()?->id ?? 3;

        // Insert the Usuários submenu
        DB::table('submenus')->insertOrIgnore([
            'menu_id' => $menu_pai_id,
            'nome' => 'Usuários',
            'rota' => 'usuarios',
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
            ->where('rota', 'usuarios')
            ->delete();
    }
};
