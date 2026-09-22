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

        $submenuId = DB::table('submenus')->where('rota', 'despesas')->value('id');

        if (! $submenuId) {
            return;
        }

        // Perfis que já possuem acesso ao Financeiro herdam acesso a Contas a Pagar
        $financeiroSubmenuId = DB::table('submenus')->where('rota', 'financeiro')->value('id');

        $perfis = collect();

        if ($financeiroSubmenuId) {
            $perfis = DB::table('perfil_submenu')
                ->where('submenu_id', $financeiroSubmenuId)
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

        $submenuId = DB::table('submenus')->where('rota', 'despesas')->value('id');

        if ($submenuId) {
            DB::table('perfil_submenu')->where('submenu_id', $submenuId)->delete();
        }
    }
};
