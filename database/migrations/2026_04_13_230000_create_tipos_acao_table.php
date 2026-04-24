<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar tabela tipos_acao
        Schema::create('tipos_acao', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // Inserir tipos padrão baseados nos valores atuais
        DB::table('tipos_acao')->insert([
            ['nome' => 'Petição', 'descricao' => 'Petição inicial ou intermediária', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Audiência', 'descricao' => 'Audiência de instrução ou conciliação', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Decisão', 'descricao' => 'Decisão judicial', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Intimação', 'descricao' => 'Intimação das partes', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Recurso', 'descricao' => 'Interposição de recurso', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Outro', 'descricao' => 'Outros tipos de andamento', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Adicionar coluna tipo_acao_id na tabela andamentos
        Schema::table('andamentos', function (Blueprint $table) {
            $table->foreignId('tipo_acao_id')->nullable()->after('tipo')->constrained('tipos_acao')->onDelete('set null');
        });

        // Migrar dados existentes: mapear valores do campo 'tipo' para tipo_acao_id
        $tiposMap = [
            'peticao' => DB::table('tipos_acao')->where('nome', 'Petição')->value('id'),
            'audiencia' => DB::table('tipos_acao')->where('nome', 'Audiência')->value('id'),
            'decisao' => DB::table('tipos_acao')->where('nome', 'Decisão')->value('id'),
            'intimacao' => DB::table('tipos_acao')->where('nome', 'Intimação')->value('id'),
            'recurso' => DB::table('tipos_acao')->where('nome', 'Recurso')->value('id'),
            'outro' => DB::table('tipos_acao')->where('nome', 'Outro')->value('id'),
        ];

        foreach ($tiposMap as $tipoAntigo => $tipoNovoId) {
            DB::table('andamentos')
                ->where('tipo', $tipoAntigo)
                ->update(['tipo_acao_id' => $tipoNovoId]);
        }

        // Opcional: remover coluna 'tipo' antiga após alguns meses
        // Schema::table('andamentos', function (Blueprint $table) {
        //     $table->dropColumn('tipo');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar dados do tipo_acao_id para campo tipo
        if (Schema::hasColumn('andamentos', 'tipo_acao_id')) {
            $andamentos = DB::table('andamentos')->whereNotNull('tipo_acao_id')->get();

            foreach ($andamentos as $andamento) {
                $tipoAcao = DB::table('tipos_acao')->find($andamento->tipo_acao_id);
                if ($tipoAcao) {
                    $tipoMapeado = match(strtolower($tipoAcao->nome)) {
                        'petição' => 'peticao',
                        'audiência' => 'audiencia',
                        'decisão' => 'decisao',
                        'intimação' => 'intimacao',
                        'recurso' => 'recurso',
                        default => 'outro',
                    };
                    DB::table('andamentos')
                        ->where('id', $andamento->id)
                        ->update(['tipo' => $tipoMapeado]);
                }
            }

            Schema::table('andamentos', function (Blueprint $table) {
                $table->dropForeign(['tipo_acao_id']);
                $table->dropColumn('tipo_acao_id');
            });
        }

        Schema::dropIfExists('tipos_acao');
    }
};
