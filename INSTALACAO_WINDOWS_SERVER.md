# Instalação no Windows Server (sem Docker)

Sistema: **Laravel 9** | PHP **8.0+** | MySQL | Node.js

---

## Pré-requisitos de Software

| Software | Versão mínima | Link |
|---|---|---|
| PHP | 8.0.2 | https://windows.php.net/download/ |
| Composer | 2.x | https://getcomposer.org/download/ |
| MySQL | 8.0 | https://dev.mysql.com/downloads/installer/ |
| Node.js | 18 LTS | https://nodejs.org/ |
| Git | qualquer | https://git-scm.com/download/win |
| Nginx | 1.24+ | https://nginx.org/en/download.html |

---

## 1. Instalação do PHP

1. Baixe o PHP 8.x **Thread Safe** (zip) para Windows em https://windows.php.net/download/
2. Extraia para `C:\php`
3. Adicione `C:\php` à variável de ambiente `PATH`
   - Painel de Controle → Sistema → Variáveis de Ambiente → `Path` → Novo → `C:\php`
4. Copie `php.ini-production` para `php.ini` na mesma pasta
5. Edite `C:\php\php.ini` e habilite as extensões abaixo (remova o `;` no início da linha):

```ini
extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
extension=exif
```

6. Configure o `extension_dir`:
```ini
extension_dir = "C:\php\ext"
```

7. Defina o limite de upload e memória (recomendado):
```ini
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 120
```

8. Teste: abra o Prompt de Comando e execute:
```cmd
php -v
```

---

## 2. Instalação do Composer

1. Baixe e execute o instalador em https://getcomposer.org/download/
2. O instalador detecta o PHP automaticamente — aponte para `C:\php\php.exe`
3. Teste:
```cmd
composer -V
```

---

## 3. Instalação do MySQL

1. Baixe o **MySQL Installer** em https://dev.mysql.com/downloads/installer/
2. Escolha o tipo **Server only** ou **Full**
3. Durante a instalação:
   - Defina senha do usuário `root`
   - Mantenha a porta padrão **3306**
4. Após instalar, abra o **MySQL Command Line Client** e crie o banco:

```sql
CREATE DATABASE juridico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'juridico_user'@'localhost' IDENTIFIED BY 'SuaSenhaAqui';
GRANT ALL PRIVILEGES ON juridico.* TO 'juridico_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 4. Instalação do Node.js

1. Baixe o instalador LTS em https://nodejs.org/
2. Execute o instalador com opções padrão
3. Teste:
```cmd
node -v
npm -v
```

---

## 5. Configuração do Projeto

### 5.1 Clonar ou copiar os arquivos

Copie os arquivos do projeto para o servidor, por exemplo em:
```
C:\nginx\www\juridico
```

Ou via Git:
```cmd
git clone <URL_DO_REPOSITORIO> C:\nginx\www\juridico
cd C:\nginx\www\juridico
```

### 5.2 Instalar dependências PHP

```cmd
cd C:\nginx\www\juridico
composer install --no-dev --optimize-autoloader
```

> Em desenvolvimento, omita `--no-dev`.

### 5.3 Configurar o arquivo `.env`

```cmd
copy .env.example .env
```

Edite `.env` com as configurações do ambiente:

```dotenv
APP_NAME="Sistema Jurídico"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://seu-dominio-ou-ip

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=juridico
DB_USERNAME=juridico_user
DB_PASSWORD=SuaSenhaAqui

SESSION_DRIVER=file
SESSION_LIFETIME=1440
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### 5.4 Gerar a chave da aplicação

```cmd
php artisan key:generate
```

### 5.5 Executar as migrations

```cmd
php artisan migrate --force
```

Se existirem seeders:
```cmd
php artisan db:seed --force
```

### 5.6 Instalar dependências Node e compilar assets

```cmd
npm install
npm run build
```

### 5.7 Publicar assets dos pacotes

```cmd
php artisan vendor:publish --tag=laravel-assets --ansi --force
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\ServiceProvider" --tag=assets
```

### 5.8 Configurar permissões de pastas

O usuário sob o qual o Nginx executa (geralmente `SYSTEM` ou o usuário configurado no `nginx.conf`) precisa de permissão de **escrita** nas pastas:

```cmd
icacls "C:\nginx\www\juridico\storage" /grant "Everyone:(OI)(CI)F" /T
icacls "C:\nginx\www\juridico\bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T
```

### 5.9 Otimizar para produção

```cmd
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Configuração do Servidor Web — Nginx

### 6.1 Instalar o Nginx

1. Baixe o Nginx para Windows (versão **Stable**) em https://nginx.org/en/download.html
2. Extraia para `C:\nginx`
3. Copie os arquivos do projeto para `C:\nginx\www\juridico` (ou ajuste o caminho conforme preferir)
4. Inicie o Nginx para testar:
```cmd
cd C:\nginx
nginx.exe
```
5. Acesse `http://localhost` — se aparecer a página padrão do Nginx, a instalação está correta
6. Para parar: `nginx.exe -s stop`

### 6.2 Configurar o PHP-FPM (FastCGI)

O Nginx não executa PHP diretamente — ele usa o PHP via FastCGI.

1. No arquivo `C:\php\php.ini`, certifique-se que está habilitado:
```ini
extension=php_fpm
```

2. Inicie o PHP em modo FastCGI (abra um Prompt de Comando e deixe rodando):
```cmd
C:\php\php-cgi.exe -b 127.0.0.1:9000
```

> **Dica:** Para rodar como serviço Windows, use o [NSSM](https://nssm.cc/download) (Non-Sucking Service Manager):
> ```cmd
> nssm install PHP-CGI "C:\php\php-cgi.exe" "-b 127.0.0.1:9000"
> nssm start PHP-CGI
> ```

### 6.3 Configurar o Virtual Host do Laravel

Edite `C:\nginx\conf\nginx.conf` e substitua o bloco `server {}` padrão (ou adicione ao final, dentro do bloco `http {}`):

```nginx
server {
    listen 80;
    server_name seu-dominio-ou-ip;

    root C:/nginx/www/juridico/public;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6.4 Recarregar o Nginx

Após salvar o arquivo de configuração:

```cmd
cd C:\nginx
nginx.exe -t          # testa a sintaxe da configuração
nginx.exe -s reload   # aplica sem derrubar o serviço
```

### 6.5 Configurar o Nginx como serviço Windows (opcional, recomendado)

Usando o NSSM:

```cmd
nssm install Nginx "C:\nginx\nginx.exe"
nssm set Nginx AppDirectory C:\nginx
nssm start Nginx
```

Para iniciar automaticamente com o Windows:
```cmd
sc config Nginx start= auto
```

---

## 7. Configurar o Link Simbólico do Storage

```cmd
php artisan storage:link
```

> Se falhar, execute o Prompt de Comando **como Administrador**.

---

## 8. Verificação Final

Acesse no navegador: `http://seu-dominio-ou-ip`

Checklist:
- [ ] Página carrega sem erro 500
- [ ] Login funciona
- [ ] Upload de arquivos funciona (testar se `storage/app/public` tem escrita)
- [ ] Listagens com DataTables carregam
- [ ] Assets CSS/JS carregam (verificar se `npm run build` foi executado)

---

## 9. Solução de Problemas Comuns

| Problema | Causa provável | Solução |
|---|---|---|
| Erro 500 | `.env` não configurado ou extensão PHP faltando | Checar `storage/logs/laravel.log` |
| Página em branco | `APP_DEBUG=false` esconde erros | Temporariamente setar `APP_DEBUG=true` |
| Assets não carregam | `npm run build` não executado | Executar `npm run build` novamente |
| Permissão negada | Nginx sem escrita em `storage/` | Rever passo 5.8 |
| `php_openssl` faltando | Extensão não habilitada no `php.ini` | Habilitar `extension=openssl` |
| Nginx retorna 502 Bad Gateway | PHP-CGI não está rodando | Iniciar `php-cgi.exe -b 127.0.0.1:9000` ou verificar serviço PHP-CGI |
| Erro de migration | Credenciais do banco incorretas | Revisar `.env` seção `DB_*` |

---

## 10. Atualizações Futuras

Para aplicar uma nova versão do sistema:

```cmd
cd C:\nginx\www\juridico

git pull origin main

composer install --no-dev --optimize-autoloader
npm install
npm run build

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
```
