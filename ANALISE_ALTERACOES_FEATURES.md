# Análise de Alterações e Novas Features - Sistema Jurídico

**Data:** 13 de abril de 2026
**Versão:** 1.0
**Status:** Planejamento

---

## Sumário Executivo

Este documento apresenta uma análise detalhada das alterações e novas funcionalidades propostas para o sistema jurídico. As mudanças visam melhorar a usabilidade, experiência do usuário e organização das informações, além de adicionar funcionalidades essenciais de gestão de processos jurídicos.

---

## 1. Módulo de Clientes - Alterações em `/clientes`, `/incluir-clientes` e `/alterar-clientes`

### 1.1 Adicionar Campo CPF

#### Descrição
Inclusão do campo CPF nas telas de cadastro e edição de clientes, permitindo a identificação única de clientes pessoa física.

#### Análise de Impacto

**Banco de Dados:**
- ✅ O campo `documento` já existe na tabela `clientes` conforme identificado no formulário
- ✅ Suporta máscaras de CPF/CNPJ
- ⚠️ Necessário criar índice para otimizar buscas por CPF
- ⚠️ Necessário adicionar validação de unicidade para CPF

**Model:**
- Arquivo: `app/Models/Cliente.php`
- ✅ Campo não está no fillable, precisa ser adicionado
- ⚠️ Implementar accessor/mutator para formatação automática
- ⚠️ Adicionar validação de CPF válido

**Controller:**
- Arquivo: `app/Http/Controllers/ClientesController.php`
- ⚠️ Adicionar regras de validação de CPF
- ⚠️ Implementar verificação de duplicidade

**Views:**
- Arquivo: `resources/views/clientes.blade.php`
- Arquivo: `resources/views/formularios/clientesFormularioClientes.blade.php`
- ✅ Campo já existe no formulário modal como `documento`
- ⚠️ Adicionar coluna na listagem principal
- ⚠️ Incluir no filtro de pesquisa

#### Pontos de Atenção
- Validar CPF utilizando algoritmo de validação padrão
- Permitir somente números (máscara aplicada no frontend)
- Considerar migração de dados existentes se houver clientes sem CPF

---

### 1.2 Remover Obrigatoriedade do Email

#### Descrição
Tornar o campo email opcional no cadastro de clientes, pois nem todos os clientes possuem email.

#### Análise de Impacto

**Banco de Dados:**
- ✅ Campo `email` já é nullable na tabela `clientes`
- ✅ Não requer migration

**Controller:**
- Arquivo: `app/Http/Controllers/ClientesController.php`
- ⚠️ Remover `required` da validação do campo email
- ⚠️ Manter validação de formato quando preenchido

**Views:**
- Arquivo: `resources/views/clientes.blade.php`
- ⚠️ Remover atributo `required` do input de email (linha ~184)
- ⚠️ Atualizar label para indicar que é opcional
- Arquivo: `resources/views/formularios/clientesFormularioClientes.blade.php`
- ⚠️ Remover obrigatoriedade do formulário modal

#### Pontos de Atenção
- Manter validação de formato de email quando informado
- Atualizar mensagens de validação
- Considerar impactos em notificações por email

---

## 2. Módulo de Processos - Alterações em `/alterar-processos` e `/incluir-processos`

### 2.1 Campo de Clientes Vinculados - Pesquisa Dinâmica

#### Descrição
Substituir o campo `select multiple` atual por um campo de pesquisa que busca clientes por nome ou CPF e permite adicionar vínculo ao processo de forma mais intuitiva.

#### Análise de Impacto

**Situação Atual:**
- Arquivo: `resources/views/processos.blade.php` (linhas ~230-238)
- Implementação: `<select multiple>` com todos os clientes listados
- **Problema:** Difícil usabilidade com muitos clientes cadastrados

**Proposta de Implementação:**

**Frontend (JavaScript):**
- Utilizar Select2 com AJAX search (biblioteca já disponível no projeto)
- Configurar busca dinâmica por nome ou CPF
- Exibir tags dos clientes selecionados
- Permitir remoção individual

**Backend (Controller):**
- Arquivo: `app/Http/Controllers/ProcessosController.php`
- ⚠️ Criar endpoint para busca AJAX: `/api/clientes/search`
- ⚠️ Implementar paginação e limite de resultados (sugestão: 10 resultados)
- ⚠️ Buscar por: `nome LIKE %term% OR cpf LIKE %term%`

**Rotas:**
- Arquivo: `routes/web.php` ou `routes/api.php`
- ⚠️ Adicionar rota GET: `/api/clientes/search?q={termo}`

**Response JSON Esperado:**
```json
{
  "results": [
    {
      "id": 1,
      "text": "João Silva - 123.456.789-00",
      "nome": "João Silva",
      "cpf": "12345678900"
    }
  ]
}
```

#### Componentes Necessários

**HTML/Blade:**
```html
<select class="form-control select2-ajax"
        multiple
        id="clientes"
        name="clientes[]"
        data-ajax-url="/api/clientes/search"
        data-minimum-input-length="3">
</select>
```

**JavaScript (Select2):**
```javascript
$('#clientes').select2({
    ajax: {
        url: '/api/clientes/search',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term,
                page: params.page || 1
            };
        },
        processResults: function (data) {
            return {
                results: data.results
            };
        }
    },
    minimumInputLength: 3,
    placeholder: 'Digite nome ou CPF do cliente'
});
```

#### Pontos de Atenção
- Performance: implementar cache para buscas frequentes
- Segurança: validar e sanitizar termo de busca
- UX: mensagem quando nenhum resultado encontrado
- Acessibilidade: manter funcionalidade com teclado

---

## 3. Módulo de Processos - Alterações na Tela `/processos`

### 3.1 Reposicionar Botões CSV e PDF para Rodapé

#### Descrição
Mover os botões de exportação (CSV e PDF) que atualmente estão junto ao formulário de pesquisa para o rodapé da tabela de resultados.

#### Análise de Impacto

**Situação Atual:**
- Arquivo: `resources/views/processos.blade.php` (linhas ~59-61)
- Localização: Dentro do form de pesquisa
- **Problema:** Posicionamento inconsistente e pode confundir usuário

**Proposta:**
- Remover botões da linha 59-61
- Adicionar nova seção após fechamento da tabela (após linha ~95)
- Manter parâmetros de query string para exportar dados filtrados

**Código Atual (Remover):**
```blade
<a href="{{ route('exportar-processos-csv', request()->query()) }}" class="btn btn-secondary">CSV</a>
<a href="{{ route('exportar-processos-pdf', request()->query()) }}" class="btn btn-outline-secondary" target="_blank">PDF</a>
```

**Código Novo (Adicionar após tabela):**
```blade
</table>
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div>
        <small class="text-muted">Total: {{ count($processos) }} processo(s)</small>
    </div>
    <div>
        <a href="{{ route('exportar-processos-csv', request()->query()) }}"
           class="btn btn-secondary">
            <i class="fas fa-file-csv"></i> Exportar CSV
        </a>
        <a href="{{ route('exportar-processos-pdf', request()->query()) }}"
           class="btn btn-outline-secondary"
           target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
    </div>
</div>
```

#### Pontos de Atenção
- Manter funcionalidade de passar filtros aplicados
- Adicionar ícones para melhor identificação visual
- Considerar exibir total de registros

---

### 3.2 Transformar "Novo Andamento" em Modal

#### Descrição
Substituir a `<div>` inline de "Novo Andamento" por um modal Bootstrap que abre ao clicar em botão "Novo Andamento".

#### Análise de Impacto

**Situação Atual:**
- Arquivo: `resources/views/processos.blade.php` (linhas ~126-149)
- Implementação: Formulário sempre visível na coluna lateral
- **Problema:** Ocupa espaço permanentemente, mesmo quando não em uso

**Proposta de Implementação:**

**1. Criar Botão de Abertura:**
```blade
<div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Timeline de Andamentos</h5>
    <button type="button"
            class="btn btn-sm btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalNovoAndamento">
        <i class="fas fa-plus"></i> Novo Andamento
    </button>
</div>
```

**2. Criar Estrutura do Modal:**
```blade
<!-- Modal Novo Andamento -->
<div class="modal fade" id="modalNovoAndamento" tabindex="-1" aria-labelledby="modalNovoAndamentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovoAndamentoLabel">Novo Andamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <!-- Conteúdo do formulário movido para cá -->
                <div class="mb-3">
                    <label for="modal_novo_tipo" class="form-label">Tipo</label>
                    <select class="form-control" id="modal_novo_tipo" name="novo_tipo" required>
                        <option value="peticao">Petição</option>
                        <option value="audiencia">Audiência</option>
                        <option value="decisao">Decisão</option>
                        <option value="intimacao">Intimação</option>
                        <option value="recurso">Recurso</option>
                        <option value="outro" selected>Outro</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="modal_nova_data_andamento" class="form-label">Data</label>
                    <input type="date" class="form-control" id="modal_nova_data_andamento"
                           name="nova_data_andamento" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label for="modal_nova_descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="modal_nova_descricao"
                              name="nova_descricao" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarAndamentoModal()">
                    <i class="fas fa-save"></i> Salvar Andamento
                </button>
            </div>
        </div>
    </div>
</div>
```

**3. Atualizar JavaScript:**
```javascript
function salvarAndamentoModal() {
    const processoId = {{ isset($processo) ? $processo->id : 'null' }};
    const tipo = $('#modal_novo_tipo').val();
    const dataAndamento = $('#modal_nova_data_andamento').val();
    const descricao = $('#modal_nova_descricao').val();

    if (!tipo || !dataAndamento || !descricao) {
        alert('Preencha todos os campos do andamento.');
        return;
    }

    $.ajax({
        url: '{{ route("incluir-andamentos") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            processo_id: processoId,
            tipo: tipo,
            data_andamento: dataAndamento,
            descricao: descricao,
            usuario_id: {{ auth()->id() }},
            created_by: {{ auth()->id() }}
        },
        success: function(response) {
            $('#modalNovoAndamento').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Erro ao salvar andamento: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
        }
    });
}

// Limpar formulário ao fechar modal
$('#modalNovoAndamento').on('hidden.bs.modal', function () {
    $('#modal_novo_tipo').val('outro');
    $('#modal_nova_data_andamento').val('{{ date("Y-m-d") }}');
    $('#modal_nova_descricao').val('');
});
```

#### Benefícios
- Melhor aproveitamento de espaço na tela
- Interface mais limpa e organizada
- Foco do usuário no formulário ao abrir modal
- Padrão consistente com outros modais do sistema

---

### 3.3 Criar Modal para Edição de Andamento

#### Descrição
Implementar funcionalidade real de edição de andamentos substituindo o alert "Funcionalidade de edição em desenvolvimento" por um modal funcional.

#### Análise de Impacto

**Situação Atual:**
- Arquivo: `resources/views/processos.blade.php` (linha ~530)
- Função `editarAndamento()` apenas exibe alert
- **Problema:** Não é possível corrigir andamentos cadastrados

**Proposta de Implementação:**

**1. Criar Estrutura do Modal:**
```blade
<!-- Modal Editar Andamento -->
<div class="modal fade" id="modalEditarAndamento" tabindex="-1" aria-labelledby="modalEditarAndamentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarAndamentoLabel">Editar Andamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editar_andamento_id">
                <div class="mb-3">
                    <label for="editar_tipo" class="form-label">Tipo</label>
                    <select class="form-control" id="editar_tipo" name="editar_tipo" required>
                        <option value="peticao">Petição</option>
                        <option value="audiencia">Audiência</option>
                        <option value="decisao">Decisão</option>
                        <option value="intimacao">Intimação</option>
                        <option value="recurso">Recurso</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editar_data_andamento" class="form-label">Data</label>
                    <input type="date" class="form-control" id="editar_data_andamento"
                           name="editar_data_andamento" required>
                </div>
                <div class="mb-3">
                    <label for="editar_descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="editar_descricao"
                              name="editar_descricao" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarEdicaoAndamento()">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
            </div>
        </div>
    </div>
</div>
```

**2. Implementar JavaScript:**
```javascript
function editarAndamento(id) {
    // Buscar dados do andamento
    $.ajax({
        url: '/api/andamentos/' + id,
        method: 'GET',
        success: function(andamento) {
            $('#editar_andamento_id').val(andamento.id);
            $('#editar_tipo').val(andamento.tipo);
            $('#editar_data_andamento').val(andamento.data_andamento);
            $('#editar_descricao').val(andamento.descricao);
            $('#modalEditarAndamento').modal('show');
        },
        error: function(xhr) {
            alert('Erro ao carregar andamento: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
        }
    });
}

function salvarEdicaoAndamento() {
    const id = $('#editar_andamento_id').val();
    const tipo = $('#editar_tipo').val();
    const dataAndamento = $('#editar_data_andamento').val();
    const descricao = $('#editar_descricao').val();

    if (!tipo || !dataAndamento || !descricao) {
        alert('Preencha todos os campos do andamento.');
        return;
    }

    $.ajax({
        url: '{{ route("alterar-andamentos") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            tipo: tipo,
            data_andamento: dataAndamento,
            descricao: descricao
        },
        success: function(response) {
            $('#modalEditarAndamento').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Erro ao salvar andamento: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
        }
    });
}
```

**3. Criar Endpoint de Consulta (Backend):**
- Arquivo: `routes/api.php`
```php
Route::get('/andamentos/{id}', [AndamentosController::class, 'show'])
    ->middleware('auth');
```

- Arquivo: `app/Http/Controllers/AndamentosController.php`
```php
public function show($id)
{
    $andamento = Andamento::findOrFail($id);

    // Verificar se usuário tem permissão
    if (!auth()->user()->can('view', $andamento)) {
        abort(403);
    }

    return response()->json([
        'id' => $andamento->id,
        'tipo' => $andamento->tipo,
        'data_andamento' => $andamento->data_andamento->format('Y-m-d'),
        'descricao' => $andamento->descricao
    ]);
}
```

**4. Atualizar Método de Update:**
- Arquivo: `app/Http/Controllers/AndamentosController.php`
```php
public function alterar(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|exists:andamentos,id',
        'tipo' => 'required|in:peticao,audiencia,decisao,intimacao,recurso,outro',
        'data_andamento' => 'required|date',
        'descricao' => 'required|string|max:5000'
    ]);

    $andamento = Andamento::findOrFail($request->id);

    // Verificar permissão
    if (!auth()->user()->can('update', $andamento)) {
        abort(403);
    }

    $andamento->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Andamento atualizado com sucesso'
    ]);
}
```

#### Pontos de Atenção
- Implementar controle de permissões (somente criador ou admin pode editar)
- Registrar log de auditoria das alterações
- Validar data de andamento (não pode ser futura)
- Considerar adicionar campo de observação sobre a edição

---

## 4. Criar CRUD para Tipo de Ação

### Descrição
Criar módulo completo de CRUD para gerenciamento de tipos de ação, substituindo o campo livre atual por relacionamento com tabela.

#### Análise de Impacto

**Situação Atual:**
- Arquivo: `database/migrations/2026_03_30_000005_create_legal_domain_core_tables.php`
- Campo `tipo_acao` na tabela `processos`: `string` livre
- **Problema:** Inconsistências na digitação, sem padronização

**Estrutura Necessária:**

### 4.1 Migration - Criar Tabela `tipos_acao`

```php
// database/migrations/2026_04_13_000001_create_tipos_acao_table.php
public function up()
{
    Schema::create('tipos_acao', function (Blueprint $table) {
        $table->id();
        $table->string('nome', 100)->unique();
        $table->string('codigo', 20)->unique()->nullable();
        $table->text('descricao')->nullable();
        $table->boolean('ativo')->default(true);
        $table->integer('ordem')->default(0);
        $table->timestamps();
    });

    // Migrar dados existentes
    DB::statement('INSERT INTO tipos_acao (nome, ativo, created_at, updated_at)
                   SELECT DISTINCT tipo_acao, true, NOW(), NOW()
                   FROM processos
                   WHERE tipo_acao IS NOT NULL AND tipo_acao != ""');
}
```

### 4.2 Migration - Alterar Tabela Processos

```php
// database/migrations/2026_04_13_000002_alter_processos_tipo_acao_foreign.php
public function up()
{
    Schema::table('processos', function (Blueprint $table) {
        // Renomear coluna antiga
        $table->renameColumn('tipo_acao', 'tipo_acao_old');
    });

    Schema::table('processos', function (Blueprint $table) {
        // Adicionar FK
        $table->foreignId('tipo_acao_id')->nullable()
              ->after('vara_tribunal')
              ->constrained('tipos_acao')
              ->nullOnDelete();
    });

    // Vincular dados migrados
    DB::statement('UPDATE processos p
                   INNER JOIN tipos_acao ta ON p.tipo_acao_old = ta.nome
                   SET p.tipo_acao_id = ta.id');
}

public function down()
{
    Schema::table('processos', function (Blueprint $table) {
        $table->dropForeign(['tipo_acao_id']);
        $table->dropColumn('tipo_acao_id');
        $table->renameColumn('tipo_acao_old', 'tipo_acao');
    });
}
```

### 4.3 Model

```php
// app/Models/TipoAcao.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAcao extends Model
{
    protected $table = 'tipos_acao';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'ativo',
        'ordem'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    public function processos()
    {
        return $this->hasMany(Processo::class, 'tipo_acao_id');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('ordem')->orderBy('nome');
    }
}
```

### 4.4 Atualizar Model Processo

```php
// Adicionar em app/Models/Processo.php
protected $fillable = [
    // ... campos existentes ...
    'tipo_acao_id', // Adicionar
];

public function tipoAcao()
{
    return $this->belongsTo(TipoAcao::class, 'tipo_acao_id');
}

// Atualizar accessor para compatibilidade
public function getTipoAcaoAttribute()
{
    return $this->tipoAcao?->nome;
}
```

### 4.5 Controller

```php
// app/Http/Controllers/TiposAcaoController.php
namespace App\Http\Controllers;

use App\Models\TipoAcao;
use Illuminate\Http\Request;

class TiposAcaoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoAcao::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $tiposAcao = $query->orderBy('ordem')->orderBy('nome')->get();

        return view('tipos-acao', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Tipos de Ação',
            'tiposAcao' => $tiposAcao,
            'rotaIncluir' => 'incluir-tipos-acao',
            'rotaAlterar' => 'alterar-tipos-acao',
            'request' => $request
        ]);
    }

    public function incluir(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'nome' => 'required|string|max:100|unique:tipos_acao,nome',
                'codigo' => 'nullable|string|max:20|unique:tipos_acao,codigo',
                'descricao' => 'nullable|string|max:1000',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean'
            ]);

            TipoAcao::create($validated);

            return redirect()->route('tipos-acao')->with('success', 'Tipo de ação criado com sucesso!');
        }

        return view('tipos-acao', [
            'tela' => 'incluir',
            'nome_tela' => 'Tipo de Ação'
        ]);
    }

    public function alterar(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'id' => 'required|exists:tipos_acao,id',
                'nome' => 'required|string|max:100|unique:tipos_acao,nome,' . $request->id,
                'codigo' => 'nullable|string|max:20|unique:tipos_acao,codigo,' . $request->id,
                'descricao' => 'nullable|string|max:1000',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean'
            ]);

            $tipoAcao = TipoAcao::findOrFail($request->id);
            $tipoAcao->update($validated);

            return redirect()->route('tipos-acao')->with('success', 'Tipo de ação atualizado com sucesso!');
        }

        $tipoAcao = TipoAcao::findOrFail($request->id);

        return view('tipos-acao', [
            'tela' => 'alterar',
            'nome_tela' => 'Tipo de Ação',
            'tipoAcao' => $tipoAcao
        ]);
    }

    public function desativar(Request $request)
    {
        $tipoAcao = TipoAcao::findOrFail($request->id);
        $tipoAcao->update(['ativo' => false]);

        return redirect()->route('tipos-acao')->with('success', 'Tipo de ação desativado com sucesso!');
    }
}
```

### 4.6 Rotas

```php
// routes/web.php
Route::match(['get', 'post'], '/tipos-acao', [TiposAcaoController::class, 'index'])
    ->name('tipos-acao')
    ->middleware('afterAuth:tipos-acao');

Route::match(['get', 'post'], '/incluir-tipos-acao', [TiposAcaoController::class, 'incluir'])
    ->name('incluir-tipos-acao')
    ->middleware('afterAuth:tipos-acao');

Route::match(['get', 'post'], '/alterar-tipos-acao', [TiposAcaoController::class, 'alterar'])
    ->name('alterar-tipos-acao')
    ->middleware('afterAuth:tipos-acao');

Route::post('/desativar-tipos-acao', [TiposAcaoController::class, 'desativar'])
    ->name('desativar-tipos-acao')
    ->middleware('afterAuth:tipos-acao');
```

### 4.7 View

```blade
{{-- resources/views/tipos-acao.blade.php --}}
@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@if(isset($tela) && $tela == 'pesquisa')
    @section('content_header')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11">Pesquisa de {{ $nome_tela }}</h1>
            <div class="col-sm-1">
                @include('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir])
            </div>
        </div>
    @stop

    @section('content')
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="tipos-acao" method="get" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="nome" class="form-control"
                           placeholder="Nome do tipo de ação"
                           value="{{ $request->input('nome') }}">
                </div>
                <div class="col-md-2">
                    <select name="ativo" class="form-control">
                        <option value="">Status</option>
                        <option value="1" {{ $request->input('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ $request->input('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Ordem</th>
                    <th>Processos</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tiposAcao as $tipo)
                    <tr>
                        <td>{{ $tipo->id }}</td>
                        <td>{{ $tipo->codigo }}</td>
                        <td>
                            <a href="{{ route($rotaAlterar, ['id' => $tipo->id]) }}">
                                {{ $tipo->nome }}
                            </a>
                        </td>
                        <td>{{ $tipo->ordem }}</td>
                        <td>{{ $tipo->processos->count() }}</td>
                        <td>
                            <span class="badge badge-{{ $tipo->ativo ? 'success' : 'secondary' }}">
                                {{ $tipo->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route($rotaAlterar, ['id' => $tipo->id]) }}"
                               class="btn btn-sm btn-link">Editar</a>
                            @if($tipo->ativo)
                                <form action="{{ route('desativar-tipos-acao') }}"
                                      method="post" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $tipo->id }}">
                                    <button type="submit" class="btn btn-sm btn-link text-danger">
                                        Desativar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @stop
@else
    @section('content_header')
        <h1>{{ $tela == 'alterar' ? 'Alteração de' : 'Inclusão de' }} {{ $nome_tela }}</h1>
    @stop

    @section('content')
        <form action="{{ $tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir) }}"
              method="post">
            @csrf
            @if($tela == 'alterar')
                <input type="hidden" name="id" value="{{ $tipoAcao->id }}">
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome *</label>
                        <input type="text"
                               class="form-control @error('nome') is-invalid @enderror"
                               id="nome"
                               name="nome"
                               value="{{ old('nome', $tipoAcao->nome ?? '') }}"
                               required>
                        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="text"
                               class="form-control @error('codigo') is-invalid @enderror"
                               id="codigo"
                               name="codigo"
                               value="{{ old('codigo', $tipoAcao->codigo ?? '') }}">
                        @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ordem">Ordem</label>
                        <input type="number"
                               class="form-control @error('ordem') is-invalid @enderror"
                               id="ordem"
                               name="ordem"
                               min="0"
                               value="{{ old('ordem', $tipoAcao->ordem ?? 0) }}">
                        @error('ordem')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                  id="descricao"
                                  name="descricao"
                                  rows="3">{{ old('descricao', $tipoAcao->descricao ?? '') }}</textarea>
                        @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                               type="checkbox"
                               id="ativo"
                               name="ativo"
                               value="1"
                               {{ old('ativo', $tipoAcao->ativo ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-secondary"
                            onclick="window.history.back();">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    @stop
@endif
```

### 4.8 Atualizar Formulário de Processos

```blade
{{-- Em resources/views/processos.blade.php --}}
{{-- Substituir input text por select --}}
<div class="col-md-4">
    <label for="tipo_acao_id" class="form-label">Tipo de ação</label>
    <select class="form-control @error('tipo_acao_id') is-invalid @enderror"
            id="tipo_acao_id"
            name="tipo_acao_id"
            required>
        <option value="">Selecione</option>
        @foreach(($tiposAcaoOptions ?? []) as $id => $nome)
            <option value="{{ $id }}"
                    {{ (string) old('tipo_acao_id', $processo->tipo_acao_id ?? '') === (string) $id ? 'selected' : '' }}>
                {{ $nome }}
            </option>
        @endforeach
    </select>
    @error('tipo_acao_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">
        <a href="{{ route('tipos-acao') }}" target="_blank">Gerenciar tipos de ação</a>
    </small>
</div>
```

### 4.9 Atualizar ProcessosController

```php
// app/Http/Controllers/ProcessosController.php

use App\Models\TipoAcao;

public function incluir(Request $request)
{
    // ... código existente ...

    $tiposAcaoOptions = TipoAcao::ativos()->pluck('nome', 'id')->toArray();

    return view('processos', [
        // ... outros dados ...
        'tiposAcaoOptions' => $tiposAcaoOptions
    ]);
}

public function alterar(Request $request)
{
    // ... código existente ...

    $tiposAcaoOptions = TipoAcao::ativos()->pluck('nome', 'id')->toArray();

    return view('processos', [
        // ... outros dados ...
        'tiposAcaoOptions' => $tiposAcaoOptions
    ]);
}

// Atualizar validação
protected function getValidationRules()
{
    return [
        // ... outros campos ...
        'tipo_acao_id' => 'required|exists:tipos_acao,id',
    ];
}
```

### 4.10 Migration para Menu

```php
// database/migrations/2026_04_13_000003_add_tipos_acao_submenu.php
public function up()
{
    // Assumindo que existe tabela de submenu conforme estrutura do projeto
    DB::table('submenu')->insert([
        'categoria_tela_id' => DB::table('categoria_tela')
            ->where('nome', 'Cadastros')
            ->value('id'),
        'nome' => 'Tipos de Ação',
        'rota' => 'tipos-acao',
        'icone' => 'fas fa-balance-scale',
        'ordem' => 50,
        'ativo' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
```

#### Pontos de Atenção
- **Migração de dados:** Executar com cuidado em produção
- **Backup:** Fazer backup antes de alterar estrutura
- **Performance:** Índices adequados nas FKs
- **Validação:** Não permitir exclusão se houver processos vinculados
- **Sementes:** Popular com tipos comuns (Ação Trabalhista, Ação Civil, etc.)

---

## 5. Excluir Submenu de Andamentos

### Descrição
Remover o item "Andamentos" do menu lateral, pois andamentos são gerenciados apenas dentro dos processos.

#### Análise de Impacto

**Situação Atual:**
- Existe rota `/andamentos` (routes/web.php linha 79-90)
- View `resources/views/andamentos.blade.php`
- Controller `app/Http/Controllers/AndamentosController.php`
- **Problema:** Redundância e possível confusão, andamentos devem estar contextualizados ao processo

**Ações Necessárias:**

### 5.1 Remover do Menu (Database)

```php
// database/migrations/2026_04_13_000004_remove_andamentos_submenu.php
public function up()
{
    DB::table('submenu')->where('rota', 'andamentos')->delete();

    // Remover permissões relacionadas se existirem
    DB::table('perfis_submenu')
        ->whereIn('submenu_id', function($query) {
            $query->select('id')
                  ->from('submenu')
                  ->where('rota', 'andamentos');
        })
        ->delete();
}

public function down()
{
    DB::table('submenu')->insert([
        'categoria_tela_id' => DB::table('categoria_tela')
            ->where('nome', 'Processos') // ou categoria apropriada
            ->value('id'),
        'nome' => 'Andamentos',
        'rota' => 'andamentos',
        'icone' => 'fas fa-list',
        'ordem' => 30,
        'ativo' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
```

### 5.2 Comentar/Remover Rotas (Opcional)

**Opção 1 - Comentar (Recomendado para manter código):**
```php
// routes/web.php
/*
Route::match(['get', 'post'], '/andamentos', [App\Http\Controllers\AndamentosController::class, 'index'])
    ->name('andamentos')
    ->middleware('afterAuth:andamentos');

Route::match(['get', 'post'], '/alterar-andamentos', [App\Http\Controllers\AndamentosController::class, 'alterar'])
    ->name('alterar-andamentos')
    ->middleware('afterAuth:andamentos');

Route::match(['get', 'post'], '/incluir-andamentos', [App\Http\Controllers\AndamentosController::class, 'incluir'])
    ->name('incluir-andamentos')
    ->middleware('afterAuth:andamentos');

Route::match(['get', 'post'], '/incluir-andamento', [App\Http\Controllers\AndamentosController::class, 'incluir'])
    ->name('incluir-andamento')
    ->middleware('afterAuth:andamentos');
*/
```

**Opção 2 - Manter apenas rotas AJAX:**
```php
// Remover rotas de visualização, manter apenas APIs
Route::post('/incluir-andamentos', [App\Http\Controllers\AndamentosController::class, 'incluir'])
    ->name('incluir-andamentos')
    ->middleware('auth');

Route::post('/alterar-andamentos', [App\Http\Controllers\AndamentosController::class, 'alterar'])
    ->name('alterar-andamentos')
    ->middleware('auth');

Route::get('/api/andamentos/{id}', [App\Http\Controllers\AndamentosController::class, 'show'])
    ->name('api.andamentos.show')
    ->middleware('auth');
```

### 5.3 Atualizar Controller

```php
// app/Http/Controllers/AndamentosController.php

// Comentar método index() se não for mais usado
/*
public function index(Request $request)
{
    // ... código comentado ...
}
*/

// Manter apenas métodos usados via AJAX:
// - incluir()
// - alterar()
// - show() (novo, para modal de edição)
```

#### Pontos de Atenção
- ✅ **Não remover arquivos:** Manter controller e model para funcionalidades AJAX
- ✅ **Rotas API:** Manter rotas POST para inclusão/alteração via processo
- ⚠️ **Testes:** Verificar se há testes automatizados que usam essas rotas
- ⚠️ **Links diretos:** Buscar no código por links para `/andamentos`
- ⚠️ **Documentação:** Atualizar documentação se existir

---

## 6. Documentos Vinculados ao Processo

### 6.1 Mostrar Andamento Vinculado na Coluna

#### Descrição
Adicionar coluna na tabela de documentos mostrando o andamento vinculado (ex: "#Audiência 13/04/2026").

#### Análise de Impacto

**Situação Atual:**
- Tabela em `resources/views/processos.blade.php` (linhas ~348-379)
- Colunas: ID, Arquivo, Versão, Status, Ações
- **Problema:** Não mostra relação com andamentos

**Implementação:**

```blade
{{-- resources/views/processos.blade.php - Tabela de documentos --}}
<table class="table table-striped text-center" id="tabela-documentos">
    <thead>
        <tr>
            <th>ID</th>
            <th>Arquivo</th>
            <th>Versão</th>
            <th>Andamento</th> {{-- NOVO --}}
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse(($processo->documentos ?? []) as $documento)
            <tr>
                <td>{{ $documento->id }}</td>
                <td>{{ $documento->nome_original }}</td>
                <td>v{{ $documento->versao }}</td>
                <td> {{-- NOVO --}}
                    @if($documento->andamento)
                        <span class="badge badge-info">
                            #{{ ucfirst($documento->andamento->tipo) }}
                            {{ $documento->andamento->data_andamento->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $documento->ativo ? 'Ativo' : 'Inativo' }}</td>
                <td>
                    <!-- Ações existentes -->
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Nenhum documento vinculado a este processo.</td>
            </tr>
        @endforelse
    </tbody>
</table>
```

**CSS Sugerido:**
```css
.badge-info {
    background-color: #17a2b8;
    color: white;
    font-size: 0.85em;
}
```

#### Pontos de Atenção
- Eager loading para evitar N+1 queries: `$processo->load('documentos.andamento')`
- Tooltip com descrição completa do andamento ao hover
- Ordenar documentos por data do andamento

---

### 6.2 Remover Campo "Compartilhar com Cliente"

#### Descrição
Retirar o checkbox "Compartilhar com cliente" do formulário de upload de documentos na tela de processos.

#### Análise de Impacto

**Localização:**
- Arquivo: `resources/views/processos.blade.php` (linhas ~317-324)
- Campo: `shared_with_client`

**Alterações:**

**1. Remover do Formulário HTML:**
```blade
{{-- REMOVER estas linhas --}}
<div class="col-md-6">
    <div class="mb-3">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" id="shared_with_client"
                   name="shared_with_client" value="1" checked>
            <label class="form-check-label" for="shared_with_client">
                Compartilhar com cliente
            </label>
        </div>
    </div>
</div>
```

**2. Remover do JavaScript (uploadDocumento):**
```javascript
// REMOVER estas linhas (~linha 449)
const sharedWithClient = document.getElementById('shared_with_client').checked;
formData.append('shared_with_client', sharedWithClient ? '1' : '0');
```

**3. Definir Valor Padrão no Controller:**
```php
// app/Http/Controllers/DocumentosController.php

public function incluir(Request $request)
{
    // ... validação existente ...

    $data = $request->validated();

    // Definir valor padrão como TRUE (sempre compartilhado)
    $data['shared_with_client'] = true;

    // Ou FALSE se a regra de negócio for não compartilhar por padrão
    // $data['shared_with_client'] = false;

    Documento::create($data);

    // ... resto do código ...
}
```

#### Decisão de Negócio Necessária

**Opção A - Sempre Compartilhar:**
- Documentos sempre visíveis para clientes
- Simplifica lógica
- Valor padrão: `true`

**Opção B - Nunca Compartilhar:**
- Documentos apenas internos
- Cliente não tem acesso
- Valor padrão: `false`

**Opção C - Manter Funcionalidade, Mover para Configuração:**
- Configuração global no sistema
- Administrador define política geral
- Pode ser alterado por documento em tela dedicada

**Recomendação:** Opção A (sempre compartilhar), pois elimina complexidade e está alinhado com transparência ao cliente.

#### Pontos de Atenção
- ⚠️ **Decisão de Negócio:** Confirmar com stakeholders qual valor padrão usar
- ⚠️ **Migração:** Definir se documentos existentes devem ser atualizados
- ⚠️ **Portal do Cliente:** Verificar impacto em portal/área do cliente se existir
- ⚠️ **Documentação:** Atualizar manual do usuário

---

## 7. Alteração de Documentos - `/alterar-documentos`

### 7.1 Mostrar Preview e Nome do Documento

#### Descrição
Adicionar área de preview do documento atual e exibir claramente o nome do arquivo sendo editado.

#### Análise de Impacto

**Situação Atual:**
- Arquivo: `resources/views/documentos.blade.php` (linha ~141)
- Apenas texto pequeno: "Atual: {{ $documento->nome_original }}"
- **Problema:** Usuário não tem certeza de qual documento está alterando

**Implementação:**

```blade
{{-- resources/views/documentos.blade.php - na seção de alteração --}}
@if($tela == 'alterar')
    {{-- Adicionar após o content_header --}}
    <div class="alert alert-info mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2">
                    <i class="fas fa-file-alt"></i>
                    Documento Atual: <strong>{{ $documento->nome_original }}</strong>
                </h5>
                <div class="small">
                    <span class="me-3">
                        <i class="fas fa-cog"></i> Tipo:
                        <strong>{{ strtoupper($documento->tipo_midia ?? 'N/A') }}</strong>
                    </span>
                    <span class="me-3">
                        <i class="fas fa-hdd"></i> Tamanho:
                        <strong>{{ number_format($documento->tamanho / 1024, 2) }} KB</strong>
                    </span>
                    <span class="me-3">
                        <i class="fas fa-code-branch"></i> Versão:
                        <strong>v{{ $documento->versao }}</strong>
                    </span>
                    <span>
                        <i class="fas fa-calendar"></i> Upload:
                        <strong>{{ $documento->created_at->format('d/m/Y H:i') }}</strong>
                    </span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('preview-documentos', ['id' => $documento->id]) }}"
                   class="btn btn-primary"
                   target="_blank">
                    <i class="fas fa-eye"></i> Visualizar Documento Completo
                </a>
            </div>
        </div>
    </div>

    {{-- Preview Inline --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Preview do Documento</h5>
        </div>
        <div class="card-body">
            @if(in_array(strtolower($documento->tipo_midia), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                {{-- Preview de Imagem --}}
                <div class="text-center">
                    <img src="{{ route('preview-documentos', ['id' => $documento->id]) }}"
                         alt="{{ $documento->nome_original }}"
                         class="img-fluid"
                         style="max-height: 400px;">
                </div>
            @elseif(strtolower($documento->tipo_midia) === 'pdf')
                {{-- Preview de PDF --}}
                <iframe src="{{ route('preview-documentos', ['id' => $documento->id]) }}"
                        style="width: 100%; height: 500px; border: 1px solid #ddd;">
                </iframe>
            @else
                {{-- Outros tipos - apenas ícone e informações --}}
                <div class="text-center py-5">
                    <i class="fas fa-file fa-5x text-muted mb-3"></i>
                    <p class="text-muted">
                        Preview não disponível para este tipo de arquivo.
                        <br>
                        <a href="{{ route('preview-documentos', ['id' => $documento->id]) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-download"></i> Fazer Download
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>
@endif

{{-- Formulário de alteração abaixo... --}}
```

**CSS Adicional:**
```css
/* resources/css/adminlte-custom.css */
.documento-preview-header {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 1rem;
}

.documento-preview-header .small {
    color: #6c757d;
}

.documento-preview-header .small span {
    display: inline-block;
    margin-right: 1rem;
}

.documento-preview-header .small i {
    margin-right: 0.25rem;
}
```

#### Implementação do Preview (Controller)

```php
// app/Http/Controllers/DocumentosController.php

public function preview($id)
{
    $documento = Documento::findOrFail($id);

    // Verificar permissão
    if (!auth()->user()->can('view', $documento)) {
        abort(403);
    }

    $path = storage_path('app/' . $documento->caminho);

    if (!file_exists($path)) {
        abort(404, 'Arquivo não encontrado');
    }

    $mimeType = mime_content_type($path);

    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $documento->nome_original . '"'
    ]);
}
```

**Rota:**
```php
// routes/web.php
Route::get('/preview-documentos/{id}', [DocumentosController::class, 'preview'])
    ->name('preview-documentos')
    ->middleware('auth');
```

#### Pontos de Atenção
- **Segurança:** Validar permissões antes de mostrar preview
- **Performance:** Implementar cache de preview para PDFs grandes
- **Tipos de arquivo:** Testar com diferentes extensões
- **Mobile:** Preview responsivo
- **Loading:** Adicionar indicador de carregamento para arquivos grandes

---

### 7.2 Remover Campo "Compartilhar com Cliente"

#### Descrição
Retirar o checkbox "Compartilhar com cliente" da tela de alteração de documentos.

#### Análise de Impacto

**Localização:**
- Arquivo: `resources/views/documentos.blade.php` (linhas ~183-187)

**Alterações:**

```blade
{{-- REMOVER este bloco --}}
<div class="col-md-4">
    <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" value="1"
               id="shared_with_client" name="shared_with_client"
               {{ old('shared_with_client', $documento->shared_with_client ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="shared_with_client">
            Compartilhar com cliente
        </label>
    </div>
</div>
```

**Controller - Manter Valor Atual:**
```php
// app/Http/Controllers/DocumentosController.php

public function alterar(Request $request)
{
    // ... validação ...

    $documento = Documento::findOrFail($request->id);

    $data = $request->validated();

    // Não alterar shared_with_client - manter valor atual
    unset($data['shared_with_client']);

    $documento->update($data);

    // ... resto do código ...
}
```

#### Alinhamento com Item 6.2
Esta alteração está alinhada com a remoção do campo na tela de inclusão. Mantém consistência na interface.

---

## 8. Resumo de Impacto Técnico

### 8.1 Banco de Dados

| Tabela | Ação | Descrição |
|--------|------|-----------|
| `clientes` | ALTER | Adicionar índice em `documento` (CPF) |
| `tipos_acao` | CREATE | Nova tabela para tipos de ação |
| `processos` | ALTER | Adicionar FK `tipo_acao_id` |
| `submenu` | DELETE | Remover item "Andamentos" |

### 8.2 Arquivos Novos

```
app/Models/TipoAcao.php
app/Http/Controllers/TiposAcaoController.php
resources/views/tipos-acao.blade.php
database/migrations/2026_04_13_000001_create_tipos_acao_table.php
database/migrations/2026_04_13_000002_alter_processos_tipo_acao_foreign.php
database/migrations/2026_04_13_000003_add_tipos_acao_submenu.php
database/migrations/2026_04_13_000004_remove_andamentos_submenu.php
```

### 8.3 Arquivos Modificados

```
app/Models/Cliente.php
app/Models/Processo.php
app/Http/Controllers/ClientesController.php
app/Http/Controllers/ProcessosController.php
app/Http/Controllers/DocumentosController.php
app/Http/Controllers/AndamentosController.php
resources/views/clientes.blade.php
resources/views/formularios/clientesFormularioClientes.blade.php
resources/views/processos.blade.php
resources/views/documentos.blade.php
routes/web.php
routes/api.php (novo ou atualizado)
public/css/adminlte-custom.css
```

### 8.4 Bibliotecas/Dependências

Já Disponíveis:
- ✅ jQuery
- ✅ Select2
- ✅ Bootstrap 5
- ✅ Font Awesome

Nenhuma nova dependência necessária.

---

## 9. Plano de Implementação Sugerido

### Fase 1 - Preparação (1-2 dias)
1. ✅ Backup completo do banco de dados
2. ✅ Criação de branch git: `feature/melhorias-sistema-juridico`
3. ✅ Revisão de dependências
4. ✅ Configuração de ambiente de testes

### Fase 2 - Features Independentes (3-4 dias)
**Prioridade Alta - Baixo Risco:**
1. Item 1.2: Remover obrigatoriedade email (30 min)
2. Item 3.1: Mover botões CSV/PDF (1 hora)
3. Item 6.2 e 7.2: Remover "Compartilhar com cliente" (1 hora)
4. Item 5: Excluir submenu Andamentos (1 hora)
5. Item 6.1: Coluna de andamento em documentos (2 horas)

### Fase 3 - Features Complexas - Parte 1 (5-7 dias)
**Prioridade Alta - Médio Risco:**
1. Item 1.1: Adicionar campo CPF (3 horas)
   - Migration e índices
   - Validação
   - Views
   - Testes
2. Item 7.1: Preview de documentos (1 dia)
   - Implementação preview
   - Testes com diferentes tipos
   - Ajustes responsivos

### Fase 4 - Features Complexas - Parte 2 (7-10 dias)
**Prioridade Média - Alto Risco:**
1. Item 4: CRUD Tipo de Ação (3-4 dias)
   - Migrations
   - Model e Controller
   - Views
   - Migração de dados
   - Atualização formulário processos
   - Testes completos

2. Item 2.1: Campo pesquisa clientes (2-3 dias)
   - Endpoint AJAX
   - Integração Select2
   - Testes de performance
   - UX refinements

### Fase 5 - Modais de Andamentos (3-4 dias)
**Prioridade Média - Médio Risco:**
1. Item 3.2: Modal Novo Andamento (1-2 dias)
   - Criação do modal
   - JavaScript
   - Testes funcionais

2. Item 3.3: Modal Edição Andamento (2 dias)
   - API endpoint
   - Modal de edição
   - Validações
   - Testes

### Fase 6 - Testes e Refinamentos (3-5 dias)
1. ✅ Testes de integração
2. ✅ Testes de regressão
3. ✅ Ajustes de UX/UI
4. ✅ Testes de performance
5. ✅ Validação com usuários

### Fase 7 - Deploy (1-2 dias)
1. ✅ Merge para staging
2. ✅ Testes em ambiente staging
3. ✅ Preparação scripts de produção
4. ✅ Deploy em produção
5. ✅ Monitoramento pós-deploy
6. ✅ Treinamento usuários

**Tempo Total Estimado: 23-35 dias úteis**

---

## 10. Riscos e Mitigações

| Risco | Impacto | Probabilidade | Mitigação |
|-------|---------|---------------|-----------|
| Perda de dados na migração tipo_acao | Alto | Baixo | Backup, testes extensivos, rollback plan |
| Performance degradada com Select2 AJAX | Médio | Médio | Índices, cache, limitação de resultados |
| Incompatibilidade preview de documentos | Médio | Médio | Testes com múltiplos formatos, fallback |
| Conflitos em banco com outros desenvolvedores | Alto | Baixo | Coordenação, migrations versionadas |
| Quebra de funcionalidade existente | Alto | Médio | Testes de regressão abrangentes |
| CPF duplicado em cadastros antigos | Médio | Médio | Validação pré-migration, limpeza dados |

---

## 11. Checklist de Validação

### Pré-Implementação
- [ ] Aprovação de stakeholders
- [ ] Backup de banco de dados
- [ ] Ambiente de testes configurado
- [ ] Branch git criada

### Durante Implementação
- [ ] Code review de cada feature
- [ ] Testes unitários escritos
- [ ] Testes de integração passando
- [ ] Documentação de código

### Pré-Deploy
- [ ] Testes em staging completos
- [ ] Validação com usuários-chave
- [ ] Script de rollback preparado
- [ ] Documentação de usuário atualizada

### Pós-Deploy
- [ ] Monitoramento de erros (primeiras 24h)
- [ ] Feedback de usuários coletado
- [ ] Performance monitorada
- [ ] Ajustes críticos aplicados

---

## 12. Métricas de Sucesso

1. **Usabilidade:**
   - Redução de 50% no tempo de cadastro de processos
   - Redução de 70% em erros de digitação de tipo de ação
   - Satisfação do usuário ≥ 8/10

2. **Performance:**
   - Tempo de carregamento de páginas < 2s
   - Busca de clientes AJAX < 500ms
   - Preview de documentos < 3s

3. **Adoção:**
   - 100% de processos novos usando tipo de ação padronizado
   - 90% de clientes com CPF cadastrado em 30 dias
   - Zero chamados de suporte sobre "Compartilhar com cliente"

---

## 13. Considerações Finais

Este documento apresenta uma análise abrangente das alterações propostas. As mudanças visam:

✅ **Melhorar a experiência do usuário** com interfaces mais intuitivas
✅ **Padronizar informações** através de tabelas relacionais
✅ **Simplificar operações** removendo campos desnecessários
✅ **Aumentar produtividade** com buscas dinâmicas e modais
✅ **Manter integridade** dos dados com validações adequadas

### Próximos Passos Recomendados

1. **Revisão com stakeholders** para validar prioridades
2. **Definição de sprints** conforme plano de implementação
3. **Alocação de recursos** (desenvolvedores, QA)
4. **Início da Fase 1** (preparação)

### Contato para Dúvidas

Para esclarecimentos técnicos sobre esta análise, consulte a equipe de desenvolvimento.

---

**Documento elaborado em:** 13/04/2026
**Versão:** 1.0
**Próxima revisão:** Após aprovação de stakeholders

