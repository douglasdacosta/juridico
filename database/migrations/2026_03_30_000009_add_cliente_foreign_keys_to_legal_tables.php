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

        if (Schema::hasTable('cliente_responsavel')) {
            Schema::table('cliente_responsavel', function (Blueprint $table) {
                $table->foreign('cliente_id')
                    ->references('id')
                    ->on('clientes')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('processo_cliente')) {
            Schema::table('processo_cliente', function (Blueprint $table) {
                $table->foreign('cliente_id')
                    ->references('id')
                    ->on('clientes')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('documentos')) {
            Schema::table('documentos', function (Blueprint $table) {
                $table->foreign('cliente_id')
                    ->references('id')
                    ->on('clientes')
                    ->nullOnDelete();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('cliente_responsavel')) {
            Schema::table('cliente_responsavel', function (Blueprint $table) {
                $table->dropForeign(['cliente_id']);
            });
        }

        if (Schema::hasTable('processo_cliente')) {
            Schema::table('processo_cliente', function (Blueprint $table) {
                $table->dropForeign(['cliente_id']);
            });
        }

        if (Schema::hasTable('documentos')) {
            Schema::table('documentos', function (Blueprint $table) {
                $table->dropForeign(['cliente_id']);
            });
        }
    }
};
