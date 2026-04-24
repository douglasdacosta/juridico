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

        $now = now();

        $submenus = [
            [
                'menu_id' => 1,
                'nome' => 'Filiais',
                'rota' => 'filiais',
                'icon' => 'fa fa-fw fa-angle-right',
                'label' => '',
                'label_color' => null,
                'icon_color' => 'grey',
                'ordem' => 0,
                'ativo' => true,
            ],
            [
                'menu_id' => 2,
                'nome' => 'Processos',
                'rota' => 'processos',
                'icon' => 'fa fa-fw fa-angle-right',
                'label' => '',
                'label_color' => null,
                'icon_color' => 'grey',
                'ordem' => 0,
                'ativo' => true,
            ],
            [
                'menu_id' => 2,
                'nome' => 'Andamentos',
                'rota' => 'andamentos',
                'icon' => 'fa fa-fw fa-angle-right',
                'label' => '',
                'label_color' => null,
                'icon_color' => 'grey',
                'ordem' => 0,
                'ativo' => true,
            ],
            [
                'menu_id' => 2,
                'nome' => 'Documentos',
                'rota' => 'documentos',
                'icon' => 'fa fa-fw fa-angle-right',
                'label' => '',
                'label_color' => null,
                'icon_color' => 'grey',
                'ordem' => 0,
                'ativo' => true,
            ],
        ];

        foreach ($submenus as $submenu) {
            DB::table('submenus')->updateOrInsert(
                ['rota' => $submenu['rota']],
                array_merge($submenu, ['updated_at' => $now, 'created_at' => $now])
            );
        }

        if (! Schema::hasTable('perfil_submenu')) {
            return;
        }

        $submenuIds = DB::table('submenus')
            ->whereIn('rota', ['filiais', 'processos', 'andamentos', 'documentos'])
            ->pluck('id');

        foreach ($submenuIds as $submenuId) {
            DB::table('perfil_submenu')->updateOrInsert(
                ['perfil_id' => 1, 'submenu_id' => $submenuId],
                ['ativo' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    public function down()
    {
        if (! Schema::hasTable('submenus')) {
            return;
        }

        $rotas = ['filiais', 'processos', 'andamentos', 'documentos'];

        if (Schema::hasTable('perfil_submenu')) {
            $submenuIds = DB::table('submenus')->whereIn('rota', $rotas)->pluck('id');

            if ($submenuIds->isNotEmpty()) {
                DB::table('perfil_submenu')
                    ->where('perfil_id', 1)
                    ->whereIn('submenu_id', $submenuIds)
                    ->delete();
            }
        }

        DB::table('submenus')->whereIn('rota', $rotas)->delete();
    }
};
