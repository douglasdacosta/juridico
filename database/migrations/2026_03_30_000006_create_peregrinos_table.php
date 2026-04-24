<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clientes')) {
            return;
        }

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 12)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('status', 1)->default('A');
            $table->timestamp('lgpd_consent_at')->nullable();
            $table->text('lgpd_purpose')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
