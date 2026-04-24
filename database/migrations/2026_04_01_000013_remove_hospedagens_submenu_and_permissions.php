<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('submenus')) {
            return;
        }

        $submenuIds = DB::table('submenus')
            ->where('rota', 'hospedagens')
            ->pluck('id');

        if ($submenuIds->isEmpty()) {
            return;
        }

        if (Schema::hasTable('perfil_submenu')) {
            DB::table('perfil_submenu')
                ->whereIn('submenu_id', $submenuIds)
                ->delete();
        }

        if (Schema::hasTable('permissoes_perfis')) {
            DB::table('permissoes_perfis')
                ->whereIn('submenus_id', $submenuIds)
                ->delete();
        }

        DB::table('submenus')
            ->whereIn('id', $submenuIds)
            ->delete();
    }

    public function down()
    {
        if (! Schema::hasTable('submenus')) {
            return;
        }

        $existing = DB::table('submenus')->where('rota', 'hospedagens')->first();

        if ($existing) {
            return;
        }

        $now = now();

        DB::table('submenus')->insert([
            'menu_id' => 1,
            'nome' => 'Hospedagens',
            'rota' => 'hospedagens',
            'icon' => 'fa fa-fw fa-angle-right',
            'label' => '',
            'label_color' => null,
            'icon_color' => 'grey',
            'ordem' => 0,
            'ativo' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
};
