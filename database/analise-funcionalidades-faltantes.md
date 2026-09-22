# Análise de Funcionalidades Ausentes — Comparativo com `refatoração.md`

**Base de comparação:** descrição comercial em [`database/refatoração.md`](refatoração.md)
**Método:** inspeção de `app/Http/Controllers`, `app/Models`, `routes/web.php`, `routes/api.php`, `database/migrations` e `resources/views`.

Legenda: ✅ Existe e funcional | ⚠️ Existe parcialmente / há apenas a base | ❌ Não existe nenhuma implementação

---

## 1. Dashboard Estratégico

| Recurso anunciado | Status | Evidência |
|---|---|---|
| Processos ativos | ✅ | `HomeController::index` calcula `processos_ativos`, `processos_encerrados`, `total_processos` |
| Próximos prazos e compromissos | ❌ | Não existe tabela/model de compromissos, prazos ou agenda. `Andamento` é só um log histórico, sem relação com "prazo futuro" |
| Entradas e saídas do mês | ❌ | `HomeController` nem consulta `Financeiro`; não há agregação mensal de receitas/despesas |
| Indicadores financeiros | ⚠️ | Existem KPIs de processos/clientes/documentos, mas nenhum indicador financeiro (a receber, recebido, inadimplência) no dashboard |
| Gráficos de desempenho | ❌ | Nenhuma lib de gráficos (Chart.js, ApexCharts etc.) referenciada em nenhuma view; `grep` por "chart"/"canvas" não retorna nada |

**Funções que faltam criar:** `HomeController@dashboardFinanceiro`, `HomeController@proximosPrazos`, componente de gráficos, agregações mensais em `Financeiro`.

---

## 2. Gestão Completa de Processos

| Recurso anunciado | Status | Evidência |
|---|---|---|
| Cadastro detalhado de processos | ✅ | `ProcessosController` (index/incluir/alterar) completo |
| Integração com DataJud | ❌ | Zero ocorrências de "datajud" no projeto — nenhum client HTTP, job ou service |
| Histórico cronológico de movimentações | ✅ | `AndamentosController`, model `Andamento`, timeline em `processos.blade.php` |
| Controle do que o cliente pode visualizar | ⚠️ | Campo `shared_with_client` existe em `Documento` (migration `2026_03_30_000005`), mas **não há nenhum consumidor**: sem portal do cliente, o campo não tem efeito nenhum hoje |
| Múltiplos clientes vinculados ao mesmo processo | ✅ | Relação `belongsToMany` em `Processo::clientes` |
| Gestão de documentos anexados | ✅ | `DocumentosController` (upload, versão, preview, exclusão) |

**Funções que faltam criar:** `DataJudService` (consulta/sincronização de andamentos via API pública do CNJ), enforcement real de `shared_with_client` (depende do item 3).

---

## 3. Portal Exclusivo do Cliente (item citado como "grande diferencial")

| Recurso anunciado | Status | Evidência |
|---|---|---|
| Área do cliente otimizada para celular | ❌ | Não existe nenhuma view/rota fora da área administrativa |
| Acesso seguro via CPF | ❌ | `config/auth.php` só define o guard `web` (usuários internos); não há guard/provider para `Cliente`, nem tela de login por CPF |
| Acompanhamento de processos autorizados | ❌ | Depende do portal — inexistente |
| Visualização de parcelas pagas/pendentes | ❌ | `FinanceiroParcela` existe no backend, mas não é exposta a nenhum client-facing endpoint |
| Agenda de audiências | ❌ | Depende do módulo de Agenda (item 6), que não existe |
| Interface moderna e profissional | ❌ | Não há layout/rota para esse público |

**Este é o módulo mais distante do anunciado: 0% implementado.** Precisa de:
- `config/auth.php`: novo guard `cliente` + provider próprio
- `ClientePortalController` (login por CPF, dashboard do cliente, listagem de processos/parcelas/audiências)
- Middleware de autorização (o cliente só vê o que foi marcado como `shared_with_client`)
- Layout dedicado (`resources/views/portal/*`)

---

## 4. Financeiro Avançado

| Recurso anunciado | Status | Evidência |
|---|---|---|
| Fluxo de caixa completo | ❌ | `FinanceiroController` só lista lançamentos (a receber de clientes); não há relatório de fluxo de caixa (entradas vs. saídas ao longo do tempo) |
| Contas a pagar e receber | ⚠️ | Só existe o lado "a receber" (honorários/valor da causa vinculados a `Cliente`). Não há modelagem de **despesas/contas a pagar** do escritório |
| Gestão de honorários parcelados | ✅ | `Financeiro::parcelado`, `FinanceiroParcela`, geração de parcelas em `incluir()` |
| Controle de inadimplência | ⚠️ | `Financeiro::computeStatus()` calcula `vencido`/`em_dia`, mas não há tela/relatório dedicado de inadimplência nem alertas |
| Cobrança automatizada via WhatsApp | ❌ | Nenhuma integração (Twilio, Z-API, WhatsApp Cloud API etc.); nenhum `Job`/`Notification` de cobrança agendada |
| Mensagens personalizadas com nome e valor automático | ❌ | Depende do item anterior — inexistente |

**Funções que faltam criar:** `Despesa`/`ContaPagar` model + controller, `WhatsAppCobrancaService`, `Job` agendado (`app/Console/Commands` + scheduler) para disparo de cobrança em parcelas vencidas, relatório de fluxo de caixa (`FinanceiroController@fluxoCaixa`).

---

## 5. Automação de Documentos

| Recurso anunciado | Status | Evidência |
|---|---|---|
| Modelos de contratos e petições | ❌ | Não existe tabela/model de "template"/"modelo"; `DocumentosController` só trata upload de arquivo já pronto |
| Preenchimento automático com dados do cliente | ❌ | Sem motor de merge de variáveis (`{{cliente.nome}}` etc.) |
| Editor integrado | ❌ | Nenhum rich-text editor (TinyMCE, CKEditor, Quill) no projeto |
| Geração automática vinculada ao processo | ❌ | Sem geração de documento a partir de modelo — hoje é só upload manual |

**Módulo praticamente inexistente.** O que existe hoje (`documentos.blade.php`, `DocumentosController`) é gestão de arquivos anexados, não automação/geração. Precisa de:
- `ModeloDocumento` (model + migration) com corpo em HTML/placeholders
- Editor (ex.: TinyMCE via CDN, já compatível com o AdminLTE)
- `ModeloDocumentoController` (CRUD de modelos)
- Serviço de merge de variáveis + geração de PDF (`GeradorDocumentoService`)

---

## 6. Agenda Inteligente

| Recurso anunciado | Status | Evidência |
|---|---|---|
| Calendário jurídico completo | ❌ | Nenhuma migration/tabela de agenda/calendário |
| Prazos fatais organizados | ❌ | Não há campo de "prazo" com alerta; `Andamento` é histórico passado, não prazo futuro |
| Compromissos vinculados a processos | ❌ | Não existe entidade "compromisso" |
| Controle de status | ❌ | Consequentemente também não existe |

**Módulo 100% inexistente.** É necessário criar do zero:
- Migration `compromissos` (tipo, data/hora, processo_id, status, responsável)
- `Compromisso` model + `CompromissosController`
- View de calendário (ex.: FullCalendar via CDN)
- Notificações de prazo (e-mail/painel)

---

## 7. Relatórios Profissionais

| Recurso anunciado | Status | Evidência |
|---|---|---|
| Relatórios financeiros | ❌ | Não há relatório agregado, só a listagem/filtro padrão do CRUD financeiro |
| Relatórios de processos | ❌ | Idem — só listagem + exportação simples |
| Exportação em PDF com logo do escritório | ⚠️ | `exportPrint()` em `ProcessosController`/`ClientesController` retorna uma **view HTML para impressão do navegador** (`resources/views/exports/*-print.blade.php`), não um PDF gerado no servidor. Não há logo/timbre do escritório em nenhuma dessas views |
| Exportação para Excel | ❌ | `maatwebsite/excel` está no `composer.json`, mas **não é usado em nenhum lugar do código** (`grep` por `Maatwebsite`/`Excel::` não retorna nada) — dependência declarada e não implementada |
| Gráficos dinâmicos | ❌ | Mesma ausência de lib de gráficos do item 1 |

**Funções que faltam criar:** `RelatoriosController` (financeiro e processos), geração real de PDF (ex.: `barryvdh/laravel-dompdf` ou `spatie/laravel-pdf`, já que `exportPrint` hoje não gera PDF real), classes `Export` do Maatwebsite (`ProcessosExport`, `FinanceiroExport`) já que a dependência existe mas está órfã, template com logo do escritório (usar `Filial`/`Settings` para logo).

---

## Resumo Executivo

| Módulo | % aproximado de implementação |
|---|---|
| Dashboard Estratégico | ~30% (só contadores básicos) |
| Gestão de Processos | ~85% (falta só DataJud) |
| Portal do Cliente | **0%** |
| Financeiro Avançado | ~40% (falta fluxo de caixa, contas a pagar, WhatsApp) |
| Automação de Documentos | **0%** (existe só upload de arquivo) |
| Agenda Inteligente | **0%** |
| Relatórios Profissionais | ~15% (só CSV e print HTML) |

Os três módulos citados no material comercial como diferenciais competitivos — **Portal do Cliente**, **Automação de Documentos** e **Agenda Inteligente** — não têm nenhuma linha de código de suporte hoje. O sistema atual é essencialmente um CRUD jurídico (clientes, processos, andamentos, documentos anexados, financeiro básico) sobre AdminLTE, sem os módulos de front-office, geração documental e integrações externas (DataJud, WhatsApp) descritos na página de vendas.
