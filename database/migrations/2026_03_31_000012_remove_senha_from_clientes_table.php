<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clientes')) {
            return;
        }

        if (Schema::hasColumn('clientes', 'senha')) {
            Schema::table('clientes', function ($table) {
                $table->dropColumn('senha');
            });
        }
    }

    public function down()
    {
        // Intencionalmente sem rollback para manter o schema alinhado
        // à regra de negócio atual (clientes não possuem senha).
    }
};
