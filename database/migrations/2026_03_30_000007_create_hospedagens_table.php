<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hospedagens')) {
            return;
        }

        Schema::create('hospedagens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cidade')->nullable();
            $table->string('nome');
            $table->string('menor_valor', 30)->nullable();
            $table->string('maior_valor', 30)->nullable();
            $table->string('valor_cafe', 30)->nullable();
            $table->string('desconto_parceiro', 30)->nullable();
            $table->boolean('parceiro')->default(false);
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('telefone2', 20)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospedagens');
    }
};
