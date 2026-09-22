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
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->string('categoria', 50)->nullable();
            $table->decimal('valor', 12, 2);
            $table->decimal('valor_pago', 12, 2)->nullable();
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('status', 20)->default('pendente');
            $table->foreignId('filial_id')->nullable()->constrained('filiais')->nullOnDelete();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('data_vencimento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('despesas');
    }
};
