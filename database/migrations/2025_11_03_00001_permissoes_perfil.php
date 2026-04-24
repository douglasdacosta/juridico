acoes<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permissoes_perfis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('acao_id');
            $table->unsignedBigInteger('submenus_id');
            $table->boolean('permitido')->default(false);
            $table->timestamps();
        });

        DB::table('permissoes_perfis')->insert([
            [
                'perfil_id' => 1,
                'acao_id' => '1',
                'submenus_id' => '1',
            ],
            [
                'perfil_id' => 1,
                'acao_id' => '2',
                'submenus_id' => '1',
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('agendamentos');
    }
};
