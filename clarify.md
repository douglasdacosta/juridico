# Clarify - Esclarecimentos sobre Especificações

## 🤔 Questões de Esclarecimento

### 1. Autenticação e Segurança

**Q1.1:** Qual é o tempo de expiração de sessão ideal?
- [ ] 2 horas
- [ ] 4 horas
- [ ] 8 horas (atual)
- [X] 24 horas
- [ ] Personalizado

**Q1.2:** Você quer implementar autenticação por 2FA (Two-Factor Authentication)?
- [ ] Sim, obrigatório
- [X] Sim, opcional
- [ ] Não

**Q1.3:** Qual é o limite de tentativas falhadas de login antes de bloquear?
- [ ] 3 tentativas (atual)
- [ ] 5 tentativas
- [X] 10 tentativas
- [ ] Sem limite

---

### 2. Gestão de Clientes

**Q2.1:** Clientes podem ser bloqueados/desativados sem perder histórico?
- [X] Sim, apenas marcados como inativos (atual)
- [ ] Não, devem ser totalmente removidos
- [ ] Sim, com backup

**Q2.2:** Você quer rastreamento de alterações completo (auditoria) em clientes?
- [ ] Sim, todas as mudanças
- [X] Apenas datas de criação/modificação (atual)
- [ ] Não

**Q2.3:** Clientes podem ter múltiplos responsáveis ou apenas um?
- [ ] Um único responsável
- [X] Múltiplos responsáveis
- [ ] Depende do tipo de cliente

**Q2.4:** Você precisa de histórico de comunicações (emails, chamadas) com o cliente?
- [ ] Sim
- [X] Não

---

### 3. Gestão de Processos

**Q3.1:** Um processo pode pertencer a múltiplos escritórios/filiais?
- [X] Sim
- [] Não
- [ ] Apenas processos do mesmo tribunal

**Q3.2:** Qual é a política de encerramento de processos?
- [ ] Automático após 6 meses sem movimentação?
- [X] Manual (advogado marca como encerrado)
- [ ] Ambos

**Q3.3:** Você quer notificações automáticas de prazos?
- [ ] Sim
- [X] Não
- [ ] Apenas para prazos críticos (últimos 7 dias)

**Q3.4:** Processos encerrados podem ser reabertos?
- [X] Sim
- [ ] Não
- [ ] Apenas com aprovação do administrador

---

### 4. Andamentos Processuais

**Q4.1:** Qual é a frequência esperada de andamentos?
- [ ] Diária
- [ ] Semanal
- [X] Conforme houver movimento

**Q4.2:** Você quer alertas automáticos quando um andamento é registrado?
- [ ] Sim, por email
- [ ] Sim, no sistema
- [X] Não

**Q4.3:** Andamentos podem ser editados após criação?
- [X] Sim, apenas pelo criador
- [ ] Sim, apenas por administrador
- [ ] Não, apenas exclusão (com auditoria)

---

### 5. Módulo de Arquivos

**Q5.1:** Qual deve ser o tamanho máximo de arquivo?
- [ ] 10 MB
- [ ] 25 MB
- [X] 50 MB (atual)
- [ ] 100 MB
- [ ] Sem limite

**Q5.2:** Você precisa de versionamento de documentos?
- [X] Sim, histórico completo
- [ ] Não, apenas versão atual
- [ ] Sim, mas apenas últimas 3 versões

**Q5.3:** Documentos podem ser compartilhados com clientes?
- [ ] Sim, com acesso limitado
- [X] Sim, com acesso total
- [ ] Não

**Q5.4:** Você quer backup/redundância para documentos?
- [ ] Sim, em nuvem
- [X] Sim, em servidor local
- [ ] Não

---

### 6. Relatórios e Analytics

**Q6.1:** Você precisa de relatórios de performance/analíticos?
- [ ] Sim, dashboard em tempo real
- [ ] Sim, relatórios mensais
- [X] Não

**Q6.2:** Qual é o período de retenção de dados?
- [ ] 1 ano
- [ ] 3 anos
- [ ] 5 anos
- [X] Indefinido

**Q6.3:** Você quer exportação de dados em quais formatos?
- [X] PDF (atual)
- [ ] Excel (atual)
- [X] CSV
- [ ] JSON

---

### 7. Integrações

**Q7.1:** Você precisa integrar com sistemas externos?
- [ ] Sí, tribunais/CNJ
- [ ] Sim, sistema de pagamento
- [ ] Sim, email/calendário
- [X] Não

**Q7.2:** Você quer API REST para integração com terceiros?
- [ ] Sim
- [X] Não

---

### 8. Performance e Escalabilidade

**Q8.1:** Quantos usuários simultâneos você espera?
- [X] < 10
- [ ] 10-50
- [ ] 50-100
- [ ] > 100

**Q8.2:** Qual é o volume esperado de processos?
- [] < 1.000
- [X] 1.000-10.000
- [ ] > 10.000

**Q8.3:** Você quer cache para otimizar performance?
- [ ] Sim, Redis
- [ ] Sim, em memória
- [X] Não

---

### 9. Conformidade e Compliance

**Q9.1:** Você precisa garantir conformidade com alguma regulação?
- [X] LGPD (Lei Geral de Proteção de Dados)
- [ ] ISO 27001
- [ ] Nenhuma específicaX
- [ ] Outra: ___________

**Q9.2:** Você precisa de encriptação de dados sensíveis?
- [ ] Sim, em repouso e em trânsito
- [] Sim, apenas em repouso
- [ ] Sim, apenas em trânsito
- [X] Não

**Q9.3:** Você precisa de auditoria completa de todas as ações?
- [X] Sim
- [ ] Não
- [ ] Apenas para operações críticas

---

### 10. Mobile

**Q10.1:** Você precisa de suporte mobile?
- [ ] Sim, app nativo
- [X] Sim, web responsivo
- [ ] Não, apenas desktop

**Q10.2:** Qual seria a prioridade mobile?
- [ ] MVP (consulta apenas)
- [ ] Acesso completo
- [X] Não é prioridade

---

## 📋 Resumo de Respostas

| Questão | Resposta |
|---------|----------|
| Q1.1 - Tempo de sessão | 24 horas |
| Q1.2 - 2FA | Sim, opcional |
| Q1.3 - Limite tentativas | 10 tentativas |
| Q2.1 - Bloqueio clientes | Inativação (exclusão lógica) |
| Q2.2 - Auditoria completa | Apenas criação/modificação em cliente |
| Q2.3 - Múltiplos responsáveis | Sim |
| Q2.4 - Histórico comunicação | Não |
| Q3.1 - Múltiplas filiais | Sim |
| Q3.2 - Encerramento automático | Manual |
| Q3.3 - Notificações prazos | Não |
| Q3.4 - Reabrir processos | Sim |
| Q4.1 - Frequência andamentos | Conforme movimento |
| Q4.2 - Alertas andamentos | Não |
| Q4.3 - Editar andamentos | Sim, apenas criador |
| Q5.1 - Tamanho máx arquivo | 50 MB |
| Q5.2 - Versionamento docs | Sim, histórico completo |
| Q5.3 - Compartilhar com clientes | Sim, acesso total |
| Q5.4 - Backup documentos | Sim, servidor local |
| Q6.1 - Relatórios analytics | Não |
| Q6.2 - Retenção dados | Indefinida |
| Q6.3 - Formatos exportação | PDF e CSV |
| Q7.1 - Integrações externas | Não |
| Q7.2 - API REST | Não |
| Q8.1 - Usuários simultâneos | < 10 |
| Q8.2 - Volume processos | 1.000–10.000 |
| Q8.3 - Cache | Não |
| Q9.1 - Conformidade | LGPD |
| Q9.2 - Encriptação | Não |
| Q9.3 - Auditoria completa | Sim |
| Q10.1 - Suporte mobile | Web responsivo |
| Q10.2 - Prioridade mobile | Não é prioridade |

---

## 🎯 Próximos Passos

1. ✅ Questões respondidas
2. ✅ Plano técnico criado em `speckit.plan`
3. 📋 Use `/speckit.tasks` para quebrar em tarefas
4. 🚀 Use `/speckit.implement` para executar

---

**Versão:** 1.0
**Data:** 30 de março de 2026
**Status:** Esclarecimentos Concluídos
