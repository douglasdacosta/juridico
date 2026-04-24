#!/bin/bash

# Script de inicialização do projeto Juridico
# Uso: ./init-juridico.sh

set -e

PROJECT_ROOT="/home/douglas/Documents/Sistemas/juridico"
LARADOCK_DIR="$PROJECT_ROOT/laradock"

echo "=========================================="
echo "  Inicializando Projeto Juridico"
echo "=========================================="
echo ""

# Parar containers anteriores
echo "📦 Parando containers anteriores..."
cd "$LARADOCK_DIR"
docker-compose down || true
echo "✅ Containers parados"
echo ""

# Iniciar containers
echo "🚀 Iniciando containers..."
docker-compose up -d nginx mysql workspace php-fpm phpmyadmin
echo "✅ Containers iniciados"
echo ""

# Aguardar MySQL estar pronto
echo "⏳ Aguardando inicialização do MySQL..."
sleep 10
echo "✅ MySQL pronto"
echo ""

# Executar migrations
echo "🗄️  Executando migrations do banco de dados..."
docker-compose exec -T workspace bash -c "cd /var/www && php artisan migrate --force"
echo "✅ Migrations executadas"
echo ""

# Instalar dependências npm
echo "📚 Instalando dependências Node.js..."
docker-compose exec -T workspace bash -c "cd /var/www && npm install"
echo "✅ Dependências npm instaladas"
echo ""

# Exibir informações finais
echo "=========================================="
echo "  ✅ Projeto Juridico Iniciado com Sucesso!"
echo "=========================================="
echo ""
echo "🌐 Acessar:"
echo "   Aplicação: http://localhost:8002"
echo "   PHPMyAdmin: http://localhost:8081"
echo ""
echo "📋 Próximos passos:"
echo "   - Acesse http://localhost:8002 no navegador"
echo "   - Verifique o banco em http://localhost:8081"
echo ""
echo "🛑 Para parar os containers:"
echo "   cd $LARADOCK_DIR && docker-compose down"
echo ""
