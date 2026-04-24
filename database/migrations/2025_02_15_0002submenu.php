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
        // Tabela de submenus, relacionada com a tabela menus
        Schema::create('submenus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->string('nome');
            $table->string('rota')->nullable();
            $table->string('icon',100)->nullable();
            $table->string('label',100)->nullable();
            $table->string('label_color',100)->nullable();
            $table->string('icon_color',100)->nullable();
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
        DB::table('submenus')->insert(


//             clientes
// motivos
// tecnicos
// servicos
// operacoes
// controle
            [


                [ 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Home', 'rota'=> 'home', 'icon' => 'fa fa-fw fa-angle-right', 'icon_color' => 'grey'],
                [ 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Hospedagens', 'rota'=> 'hospedagens', 'icon' => 'fa fa-fw fa-angle-right', 'icon_color' => 'grey'],
                [ 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Cadastro de Perfis', 'rota'=> 'perfis', 'icon' => 'fa fa-fw fa-angle-right', 'icon_color' => 'grey'],
                [ 'menu_id'=>'3', 'label'=>'', 'nome'=> 'Clientes', 'rota'=> 'clientes', 'icon' => 'fa fa-fw fa-angle-right', 'icon_color' => 'grey'],

            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submenus');
    }
};
