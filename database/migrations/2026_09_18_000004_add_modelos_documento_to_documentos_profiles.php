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
        if (! Schema::hasTable('submenus') || ! Schema::hasTable('perfil_submenu')) {
            return;
        }

        $submenuId = DB::table('submenus')->where('rota', 'modelos-documento')->value('id');

        if (! $submenuId) {
            return;
        }

        // Perfis que já possuem acesso a Documentos herdam acesso a Modelos de Documento
        $documentosSubmenuId = DB::table('submenus')->where('rota', 'documentos')->value('id');

        $perfis = collect();

        if ($documentosSubmenuId) {
            $perfis = DB::table('perfil_submenu')
                ->where('submenu_id', $documentosSubmenuId)
                ->distinct()
                ->pluck('perfil_id');
        }

        if ($perfis->isEmpty()) {
            $perfis = collect([1]);
        }

        foreach ($perfis as $perfilId) {
            DB::table('perfil_submenu')->insertOrIgnore([
                'perfil_id' => $perfilId,
                'submenu_id' => $submenuId,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('submenus') || ! Schema::hasTable('perfil_submenu')) {
            return;
        }

        $submenuId = DB::table('submenus')->where('rota', 'modelos-documento')->value('id');

        if ($submenuId) {
            DB::table('perfil_submenu')->where('submenu_id', $submenuId)->delete();
        }
    }
};
