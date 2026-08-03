<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('financeiros', function (Blueprint $table) {
            $table->boolean('parcelado')->default(false)->after('status_pagamento');
            $table->unsignedInteger('numero_parcelas')->nullable()->after('parcelado');
            $table->decimal('valor_parcela', 12, 2)->nullable()->after('numero_parcelas');
            $table->date('data_primeira_parcela')->nullable()->after('valor_parcela');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financeiros', function (Blueprint $table) {
            $table->dropColumn(['parcelado', 'numero_parcelas', 'valor_parcela', 'data_primeira_parcela']);
        });
    }
};
