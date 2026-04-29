<?php

use Illuminate\Database\Migrations\Migration;
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
        // Usar SQL direto para alterar a coluna tipo de ENUM para VARCHAR
        DB::statement('ALTER TABLE andamentos MODIFY tipo VARCHAR(100) NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Voltar para ENUM se necessário (comentado por segurança)
        // DB::statement("ALTER TABLE andamentos MODIFY tipo ENUM('peticao', 'audiencia', 'decisao', 'intimacao', 'recurso', 'outro') NOT NULL DEFAULT 'outro'");
    }
};

