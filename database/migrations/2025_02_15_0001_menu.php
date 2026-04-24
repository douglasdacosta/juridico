<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Tabela de menus principais
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('categoria_menu_id')->constrained('categoria_menus')->onDelete('cascade');
            $table->string('icon')->nullable();
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // Inserção de dados de exemplo na tabela menus
        DB::table('menus')->insert([
            //Lançamentos
            ['id' => 1, 'nome' => 'Cadastros', 'categoria_menu_id'=>'1' , 'icon' => 'fa fa-fw fa-user-friends', 'ordem' => 1, 'ativo' => 1],
            ['id' => 2, 'nome' => 'Controles', 'categoria_menu_id'=>'1' , 'icon' => 'fa fa-fw fa-folder', 'ordem' => 2, 'ativo' => 1],
            ['id' => 3, 'nome' => 'Configurações', 'categoria_menu_id'=>'1' , 'icon' => 'fa fa-fw fa-cog', 'ordem' => 3, 'ativo' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
