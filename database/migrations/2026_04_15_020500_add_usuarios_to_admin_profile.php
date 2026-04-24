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
        // Find the Usuários submenu
        $submenu_id = DB::table('submenus')
            ->where('rota', 'usuarios')
            ->value('id');

        // If submenu exists, add it to all profiles that have other admin submenus
        if ($submenu_id) {
            // Get all admin profiles (those that have access to Tipos de Ação)
            $perfis = DB::table('perfil_submenu')
                ->whereIn('submenu_id', [3, 9]) // Cadastro de Perfis (3) or Tipos de Ação (9)
                ->distinct('perfil_id')
                ->pluck('perfil_id');

            // If no profiles found, add to Admin profile (id: 1)
            if ($perfis->isEmpty()) {
                $perfis = collect([1]);
            }

            // Insert Usuários permission for each profile
            foreach ($perfis as $perfil_id) {
                DB::table('perfil_submenu')->insertOrIgnore([
                    'perfil_id' => $perfil_id,
                    'submenu_id' => $submenu_id,
                    'ativo' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove Usuários permission from all profiles
        $submenu_id = DB::table('submenus')
            ->where('rota', 'usuarios')
            ->value('id');

        if ($submenu_id) {
            DB::table('perfil_submenu')
                ->where('submenu_id', $submenu_id)
                ->delete();
        }
    }
};
