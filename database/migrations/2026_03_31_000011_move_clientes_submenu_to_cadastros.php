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

        DB::table('submenus')
            ->where('rota', 'clientes')
            ->update([
                'menu_id' => 1,
                'updated_at' => now(),
            ]);
    }

    public function down()
    {
        if (! Schema::hasTable('submenus')) {
            return;
        }

        DB::table('submenus')
            ->where('rota', 'clientes')
            ->update([
                'menu_id' => 3,
                'updated_at' => now(),
            ]);
    }
};
