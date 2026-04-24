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
        Schema::create('perfil_submenu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')->constrained('perfis')->onDelete('cascade');
            $table->foreignId('submenu_id')->constrained('submenus')->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('perfil_submenu')->insert(
            [
                //Lançamentos

                ['id'=> '1', 'perfil_id'=>'1', 'submenu_id'=>'1'],
                ['id'=> '2', 'perfil_id'=>'1', 'submenu_id'=>'2'],
                ['id'=> '3', 'perfil_id'=>'1', 'submenu_id'=>'3'],


            ]
        );

    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::dropIfExists('perfis_submenus');
    }
};
