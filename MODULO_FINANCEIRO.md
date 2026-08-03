# Módulo Financeiro - Especificação

**Data:** 27 de julho de 2026
**Versão:** 1.0
**Status:** Planejamento

---

## 🎯 Objetivo

Criar um novo módulo **Financeiro** para controle dos valores relacionados a cada processo jurídico (valor da causa, honorários e reembolso), permitindo o acompanhamento da situação de pagamento do cliente ao longo do tempo.

Cada lançamento financeiro deve poder ser **vinculado a um ou mais processos**, desde que todos pertençam ao **mesmo cliente**.

---

## 👤 Cenários de Uso

### US1 - Cadastrar lançamento financeiro (P1)
Como usuário autenticado, quero cadastrar um lançamento financeiro informando valor da causa, honorários, reembolso e data de pagamento, para controlar o financeiro do cliente.

### US2 - Vincular lançamento a um ou mais processos (P1)
Como usuário autenticado, quero vincular um lançamento financeiro a um ou mais processos do mesmo cliente, para consolidar valores quando o cliente tiver múltiplos processos relacionados.

### US3 - Acompanhar status de pagamento (P1)
Como usuário autenticado, quero marcar/atualizar o status do lançamento como "Em dia", "Atrasado" ou "Quitado", para saber rapidamente a situação financeira de cada cliente/processo.

### US4 - Listar e filtrar lançamentos (P2)
Como usuário autenticado, quero listar os lançamentos financeiros filtrando por cliente, processo e status, para localizar rapidamente informações de cobrança.

### US5 - Editar e excluir lançamento (P2)
Como usuário autenticado, quero editar ou excluir um lançamento financeiro, respeitando as permissões do meu perfil.

---

## 🧩 Campos do Módulo

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `cliente_id` | FK (clientes) | Sim | Cliente dono do lançamento financeiro |
| `numero_processo` | Vínculo (N:N com `processos`) | Não* | Um ou mais processos vinculados ao lançamento |
| `valor_causa` | decimal(12,2) | Sim | Valor total da causa |
| `honorarios` | decimal(12,2) | Não | Valor de honorários combinados |
| `reembolso` | decimal(12,2) | Não | Valor de reembolso de despesas (custas, deslocamento, etc.) |
| `data_pagamento` | date | Não | Data em que o pagamento foi (ou deve ser) realizado |
| `status_pagamento` | enum | Sim | `em_dia`, `atrasado`, `quitado` |
| `observacoes` | text | Não | Anotações livres sobre o lançamento |

\* Recomenda-se exigir ao menos 1 processo vinculado na criação (regra de negócio, ver abaixo).

### Opções do campo `status_pagamento`

| Valor interno | Label exibido |
|---|---|
| `em_dia` | Em dia |
| `atrasado` | Atrasado |
| `quitado` | Quitado |

---

## ✅ Regras de Negócio

- RN-001: Um lançamento financeiro pertence a **um único cliente**.
- RN-002: Os processos vinculados a um lançamento devem obrigatoriamente pertencer ao cliente selecionado (`processo.clientes` deve conter o `cliente_id` do lançamento).
- RN-003: Um lançamento pode ter **um ou mais processos** vinculados (relação N:N).
- RN-004: `valor_causa` deve ser maior que zero.
- RN-005: `honorarios` e `reembolso`, quando informados, devem ser maiores ou iguais a zero.
- RN-006: Se `status_pagamento` for `quitado`, `data_pagamento` deve ser preenchida.
- RN-007: Exclusão de lançamento deve respeitar permissões de perfil (ex.: apenas administrador/responsável financeiro).

---

## 🗄️ Modelagem de Banco de Dados (sugestão)

### Migration `create_financeiros_table`

```php
Schema::create('financeiros', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
    $table->decimal('valor_causa', 12, 2);
    $table->decimal('honorarios', 12, 2)->nullable();
    $table->decimal('reembolso', 12, 2)->nullable();
    $table->date('data_pagamento')->nullable();
    $table->string('status_pagamento')->default('em_dia'); // em_dia | atrasado | quitado
    $table->text('observacoes')->nullable();
    $table->timestamps();
});
```

### Migration `create_financeiro_processo_table` (pivot N:N)

```php
Schema::create('financeiro_processo', function (Blueprint $table) {
    $table->id();
    $table->foreignId('financeiro_id')->constrained('financeiros')->cascadeOnDelete();
    $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['financeiro_id', 'processo_id']);
});
```

### Model `app/Models/Financeiro.php`

```php
class Financeiro extends Model
{
    use HasFactory;

    protected $table = 'financeiros';

    protected $fillable = [
        'cliente_id',
        'valor_causa',
        'honorarios',
        'reembolso',
        'data_pagamento',
        'status_pagamento',
        'observacoes',
    ];

    protected $casts = [
        'valor_causa' => 'decimal:2',
        'honorarios' => 'decimal:2',
        'reembolso' => 'decimal:2',
        'data_pagamento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function processos()
    {
        return $this->belongsToMany(Processo::class, 'financeiro_processo', 'financeiro_id', 'processo_id')
            ->withTimestamps();
    }
}
```

### Ajustes nos models existentes

- `Cliente.php`: adicionar relação `financeiros()` (`hasMany`).
- `Processo.php`: adicionar relação `financeiros()` (`belongsToMany` via `financeiro_processo`).

---

## 🖥️ Telas e Rotas (sugestão, seguindo padrão atual do projeto)

| Rota | Ação |
|---|---|
| `GET /financeiro` | Listagem de lançamentos com filtro por cliente/processo/status |
| `GET /incluir-financeiro` | Formulário de novo lançamento |
| `POST /financeiro` | Salvar novo lançamento |
| `GET /alterar-financeiro/{id}` | Formulário de edição |
| `PUT /financeiro/{id}` | Atualizar lançamento |
| `DELETE /financeiro/{id}` | Excluir lançamento |

### Comportamento do formulário

1. Usuário seleciona o **cliente** primeiro.
2. Campo de **processos vinculados** (multi-select) é populado apenas com processos daquele cliente (via AJAX, reaproveitando padrão já usado em outras telas do sistema).
3. Campos de valores (`valor_causa`, `honorarios`, `reembolso`) com máscara monetária.
4. Campo `status_pagamento` como `select` com as 3 opções (Em dia / Atrasado / Quitado).
5. Campo `data_pagamento` com date picker.

---

## 📏 Critérios de Sucesso

- SC-001: É possível cadastrar um lançamento financeiro vinculado a 1 ou mais processos do mesmo cliente.
- SC-002: Não é possível vincular um processo de outro cliente ao lançamento (validação de backend).
- SC-003: Listagem permite filtrar por status (Em dia / Atrasado / Quitado).
- SC-004: Edição de status reflete corretamente na listagem.
- SC-005: Exclusão remove o lançamento e seus vínculos com processos (pivot), sem afetar os processos em si.

---

## 🚫 Fora de Escopo (nesta primeira versão)

- Geração de boletos/cobranças automáticas
- Integração com meios de pagamento (gateway, PIX, cartão)
- Parcelamento de honorários
- Relatórios financeiros consolidados/gráficos
- Notificações automáticas de atraso
