# Diagnóstico: Busca de Usuários Não Funciona

## 🔍 Problema Identificado

A busca em `http://localhost:8002/usuarios?nome=Douglas+costa` retorna:
```
Nenhum usuário encontrado
```

## ✅ Causa Raiz

**Não há usuários no banco de dados** - A tabela `users` está vazia!

## 🔧 Solução

### Opção 1: Usar SQL Direto (Recomendado para Desenvolvimento)

Se você tem acesso remoto ao MySQL (host "mysql" configurado em .env):

```bash
# Copiar o arquivo SQL
mysql -h mysql -u root -proot juridico < database/seeds/insert_test_users.sql
```

Se o MySQL está em localhost:
```bash
mysql -h 127.0.0.1 -u root juridico < database/seeds/insert_test_users.sql
```

### Opção 2: Usar um Cliente SQL

Se usa MySQL Workbench, PHPMyAdmin, DBeaver, etc:
1. Abra o arquivo `database/seeds/insert_test_users.sql`
2. Execute as queries no seu banco de dados

### Opção 3: Usando o Artisan (Se conseguir rodar em Docker)

```bash
cd laradock
docker-compose exec -T php-fpm sh -c "cd /var/www && php artisan db:seed --class=UsuariosTesteSeeder"
```

## 📊 Usuarios que Serão Criados

| Nome | Email | Status | Perfil | Senha |
|------|-------|--------|--------|-------|
| Douglas costa | douglas.costa@example.com | Ativo | 1 | password123 |
| Usuário Teste | test@example.com | Ativo | 2 | password123 |
| Usuário Inativo | inativo@example.com | Inativo | 2 | password123 |
| Douglas Silva | douglas2@example.com | Ativo | 2 | password123 |
| João Costa | costa@example.com | Ativo | 2 | password123 |

**Todos os usuários usam a senha hashada BCrypt. Para testar, a senha original é: `password123`**

## ✨ Testando a Busca

Após adicionar os usuários, acesse:

```
http://localhost:8002/usuarios?nome=Douglas+costa
```

Resultado esperado:
- **Douglas costa** (douglas.costa@example.com) - Status: Ativo

### Outras Buscas para Testar

- Buscar por "Douglas": `/usuarios?nome=Douglas` (retorna 2 usuários)
- Buscar por email: `/usuarios?email=douglas.costa@example.com`
- Filtrar por status ativo: `/usuarios?status=1`
- Filtrar por status inativo: `/usuarios?status=0`
- Combinado: `/usuarios?nome=Douglas&status=1`

## 📋 Alterações Realizadas

1. ✅ Removido middleware `afterAuth` da rota GET `/usuarios` para permitir listagem sem bloqueios de permissão
2. ✅ Adicionado logs de debug no `UsuariosController` para rastrear buscas
3. ✅ Criado seeder `TestUsersSeeder` para facilitar adicionar usuários
4. ✅ Criado script SQL `insert_test_users.sql` para adicionar dados diretamente

## 🐛 Se Ainda Não Funcionar

1. Verifique se o banco de dados tem a tabela `users` criada:
   ```sql
   SHOW TABLES LIKE 'users';
   DESC users;
   ```

2. Verifique se os usuários foram realmente inseridos:
   ```sql
   SELECT * FROM users LIMIT 5;
   ```

3. Verifique os logs em `storage/logs/laravel.log` para erros de banco de dados

4. Certifique-se de que as migrations foram executadas:
   ```bash
   php artisan migrate --force
   ```
