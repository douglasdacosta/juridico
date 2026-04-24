# Inicialização do Projeto Juridico

## 📋 Pré-requisitos

- Docker e Docker Compose instalados
- Linux/Mac ou WSL2 no Windows
- Permissões de sudo (para comandos docker)

## 🚀 Passos para Inicializar

### 1. Acessar o diretório do Laradock
```bash
cd /home/douglas/Documents/Sistemas/juridico/laradock
```

### 2. Parar containers anteriores (se houver)
```bash
docker-compose down
```

### 3. Iniciar os containers principais
```bash
docker-compose up -d nginx mysql workspace php-fpm phpmyadmin
```

**Serviços iniciados:**
- `nginx` - Servidor web (porta 80 → 8002)
- `mysql` - Banco de dados (porta 3306)
- `workspace` - Ambiente de trabalho
- `php-fpm` - Processador PHP
- `phpmyadmin` - Interface de gerenciamento do banco (porta 8081)

### 4. Aguardar inicialização completa (~5 segundos)

### 5. Executar migrations do banco de dados
```bash
docker-compose exec -T workspace bash -c "cd /var/www && php artisan migrate --force"
```

### 6. Instalar dependências Node.js
```bash
docker-compose exec -T workspace bash -c "cd /var/www && npm install"
```

## 🌐 Acessar a Aplicação

| Serviço | URL | Descrição |
|---------|-----|----------|
| Aplicação | http://localhost:8002 | CRM Projearte |
| PHPMyAdmin | http://localhost:8081 | Gerenciador de BD |
| MySQL | localhost:3306 | Banco de dados |

## 🔧 Credenciais Padrão

- **DB_HOST:** mysql
- **DB_PORT:** 3306
- **DB_DATABASE:** caminho_fe
- **DB_USERNAME:** root
- **DB_PASSWORD:** root

*Configurado em `.env` na raiz do projeto*

## 📦 Comandos Úteis

### Executar comandos Laravel dentro do container
```bash
docker-compose exec -T workspace bash -c "cd /var/www && php artisan <comando>"
```

### Ver logs dos serviços
```bash
docker-compose logs -f <serviço>
```

### Parar todos os containers
```bash
docker-compose down
```

### Acessar terminal do workspace
```bash
docker-compose exec workspace bash
```

### Compilar assets (Vite/Webpack)
```bash
docker-compose exec -T workspace bash -c "cd /var/www && npm run dev"
```

## ⚠️ Troubleshooting

### Erro: "Connection refused" no MySQL
- Aguarde 10-15 segundos para o MySQL inicializar completamente
- Verifique: `docker-compose logs mysql`

### Erro: "Unable to lock ./ibdata1"
- Execute: `docker-compose down` e inicie novamente

### Porta já em uso
- Modifique a porta em `laradock/docker-compose.yml` ou encerre o processo usando a porta

---

**Última atualização:** 30 de março de 2026
