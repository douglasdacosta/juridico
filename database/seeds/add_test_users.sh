#!/bin/bash

# Script para adicionar usuários de teste ao banco de dados
# Execute este script desta forma:
# bash database/seeds/add_test_users.sh

cd "$(dirname "$0")/../.."

echo "Adicionando usuários de teste ao banco de dados..."

# Opção 1: Se você está usando Docker com laradock
if [ -f "laradock/docker-compose.yml" ]; then
    echo "Docker detectado. Executando seeder..."
    cd laradock
    docker-compose exec -T php-fpm php artisan db:seed --class=TestUsersSeeder
    cd ..
    echo "✓ Usuários adicionados com sucesso!"
    echo ""
    echo "Usuários criados:"
    echo "  - Douglas costa (douglas.costa@example.com)"
    echo "  - Usuário Teste (test@example.com)"
    echo "  - Usuário Inativo (inativo@example.com)"
    echo "  - Douglas Silva (douglas2@example.com)"
    echo "  - João Costa (costa@example.com)"
    echo ""
    echo "Senha padrão para todos: password123"
else
    echo "Comando artisan local:"
    php artisan db:seed --class=TestUsersSeeder
fi
