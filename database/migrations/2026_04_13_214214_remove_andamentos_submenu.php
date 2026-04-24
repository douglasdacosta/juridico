<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Buscar o ID do submenu de andamentos
        $submenuId = DB::table('submenus')->where('rota', 'andamentos')->value('id');

        if ($submenuId) {
            // Remover permissões relacionadas
            DB::table('perfil_submenu')
                ->where('submenu_id', $submenuId)
                ->delete();

            // Remover o submenu
            DB::table('submenus')
                ->where('rota', 'andamentos')
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restaurar o submenu de andamentos
        $categoriaId = DB::table('categoria_tela')
            ->where('nome', 'Processos')
            ->value('id');

        if (!$categoriaId) {
            $categoriaId = DB::table('categoria_tela')
                ->where('nome', 'Gestão')
                ->value('id');
        }

        $submenuId = DB::table('submenus')->insertGetId([
            'categoria_tela_id' => $categoriaId,
            'nome' => 'Andamentos',
            'rota' => 'andamentos',
            'icone' => 'fas fa-list',
            'ordem' => 30,
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Restaurar permissões para perfis (admin e advogado)
        $perfis = DB::table('perfis')->whereIn('perfil', ['admin', 'advogado'])->pluck('id');

        foreach ($perfis as $perfilId) {
            DB::table('perfil_submenu')->insert([
                'perfil_id' => $perfilId,
                'submenu_id' => $submenuId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
};
