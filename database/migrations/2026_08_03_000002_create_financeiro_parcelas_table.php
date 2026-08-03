<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('financeiro_parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financeiro_id')->constrained('financeiros')->cascadeOnDelete();
            $table->unsignedInteger('numero')->default(1);
            $table->decimal('valor', 12, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('status', 20)->default('pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('financeiro_parcelas');
    }
};
