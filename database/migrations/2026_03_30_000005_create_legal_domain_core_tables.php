<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('filiais', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj', 20)->nullable()->unique();
            $table->string('endereco')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_processo', 50)->unique();
            $table->string('vara_tribunal');
            $table->string('tipo_acao');
            $table->date('data_abertura');
            $table->date('data_encerramento')->nullable();
            $table->enum('status', ['ativo', 'encerrado', 'suspenso', 'arquivado'])->default('ativo');
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('cliente_responsavel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('papel')->nullable();
            $table->timestamps();

            $table->unique(['cliente_id', 'user_id']);
        });

        Schema::create('processo_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->foreignId('cliente_id');
            $table->string('papel_cliente')->nullable();
            $table->timestamps();

            $table->unique(['processo_id', 'cliente_id']);
        });

        Schema::create('processo_filial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->foreignId('filial_id')->constrained('filiais')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['processo_id', 'filial_id']);
        });

        Schema::create('andamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->string('tipo', 100)->default('outro');
            $table->date('data_andamento');
            $table->text('descricao');
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_original');
            $table->string('nome_armazenado')->unique();
            $table->string('tipo_midia', 50)->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->string('caminho');
            $table->foreignId('cliente_id')->nullable();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('andamento_id')->nullable()->constrained('andamentos')->nullOnDelete();
            $table->uuid('version_group_id')->nullable();
            $table->unsignedInteger('versao')->default(1);
            $table->boolean('shared_with_client')->default(true);
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['cliente_id', 'processo_id', 'andamento_id']);
            $table->index(['version_group_id', 'versao']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('andamentos');
        Schema::dropIfExists('processo_filial');
        Schema::dropIfExists('processo_cliente');
        Schema::dropIfExists('cliente_responsavel');
        Schema::dropIfExists('processos');
        Schema::dropIfExists('filiais');
    }
};
