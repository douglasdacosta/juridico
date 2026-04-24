<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('acoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50)->unique();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('acoes')->insert([
            ['nome' => 'Incluir'],
            ['nome' => 'Alterar'],
            ['nome' => 'Excluir'],
            ['nome' => 'Duplicar'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('acoes');
    }
};
