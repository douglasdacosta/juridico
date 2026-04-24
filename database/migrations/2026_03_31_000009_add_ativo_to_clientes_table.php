<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clientes')) {
            return;
        }

        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('status');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('clientes')) {
            return;
        }

        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'ativo')) {
                $table->dropColumn('ativo');
            }
        });
    }
};
