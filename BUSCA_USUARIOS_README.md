# Adicionando Usuários de Teste para a Busca

Se a busca de usuários não está retornando resultados (mensagem "Nenhum usuário encontrado"), você precisa ter usuários no banco de dados.

## Opção 1: Usando Docker (Recomendado)

Se está usando Laradock (Docker), execute:

```bash
cd laradock
docker-compose exec -T php-fpm php artisan db:seed --class=TestUsersSeeder
```

## Opção 2: Usando o script bash

```bash
bash database/seeds/add_test_users.sh
```

## Opção 3: Executando manualmente o PHP Artisan

```bash
php artisan db:seed --class=TestUsersSeeder
```

## Usuários Criados

O seeder criará os seguintes usuários de teste:

| Nome | Email | Status | Senha |
|------|-------|--------|-------|
| Douglas costa | douglas.costa@example.com | Ativo | password123 |
| Usuário Teste | test@example.com | Ativo | password123 |
| Usuário Inativo | inativo@example.com | Inativo | password123 |
| Douglas Silva | douglas2@example.com | Ativo | password123 |
| João Costa | costa@example.com | Ativo | password123 |

## Testando a Busca

Após adicionar os usuários, acesse:

```
http://localhost:8002/usuarios?nome=Douglas+costa
```

Você deve ver:
- **Douglas costa** (douglas.costa@example.com) - Ativo

Ou teste com "Douglas" para ver 2 resultados:
```
http://localhost:8002/usuarios?nome=Douglas
```

Você verá:
- **Douglas costa** (douglas.costa@example.com) - Ativo
- **Douglas Silva** (douglas2@example.com) - Ativo

## Outras Buscas

- **Por e-mail**: `?email=douglas.costa@example.com`
- **Por status**: `?status=1` (Ativo) ou `?status=0` (Inativo)
- **Combinado**: `?nome=Douglas&status=1`

## Logs Disponíveis

Para debugar qualquer problema, verifique:
- `storage/logs/laravel.log` - Logs da aplicação
- Os logs mostram quando um usuário é buscado e quantos foram encontrados
