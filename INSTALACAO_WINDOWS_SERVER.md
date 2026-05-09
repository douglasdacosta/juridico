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
| IIS ou Apache | — | ver seção abaixo |

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
C:\inetpub\wwwroot\juridico
```

Ou via Git:
```cmd
git clone <URL_DO_REPOSITORIO> C:\inetpub\wwwroot\juridico
cd C:\inetpub\wwwroot\juridico
```

### 5.2 Instalar dependências PHP

```cmd
cd C:\inetpub\wwwroot\juridico
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

O usuário do servidor web (IIS: `IIS_IUSRS` / Apache: o usuário do serviço) precisa de permissão de **escrita** nas pastas:

```cmd
icacls "C:\inetpub\wwwroot\juridico\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\juridico\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### 5.9 Otimizar para produção

```cmd
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Configuração do Servidor Web

### Opção A — IIS (Internet Information Services)

1. Habilite o IIS via **Painel de Controle → Ativar ou desativar recursos do Windows**:
   - Web Server (IIS) → Application Development → CGI

2. Instale o **PHP Manager for IIS**: https://www.iis.net/downloads/community/2018/05/php-manager-150-for-iis-10

3. No IIS Manager:
   - Adicione o PHP como FastCGI: **PHP Manager → Register new PHP version** → aponte para `C:\php\php-cgi.exe`

4. Crie um novo **Site** apontando para `C:\inetpub\wwwroot\juridico\public`

5. Crie o arquivo `C:\inetpub\wwwroot\juridico\public\web.config`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name="Laravel" stopProcessing="true">
          <match url="^(.*)$" ignoreCase="false" />
          <conditions logicalGrouping="MatchAll">
            <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
          </conditions>
          <action type="Rewrite" url="index.php" appendQueryString="true" />
        </rule>
      </rules>
    </rewrite>
    <httpErrors errorMode="Detailed" />
  </system.webServer>
</configuration>
```

6. Instale o módulo **URL Rewrite** do IIS: https://www.iis.net/downloads/microsoft/url-rewrite

### Opção B — Apache (XAMPP / Apache Lounge)

1. Baixe o Apache para Windows em https://www.apachelounge.com/download/
2. Configure o Virtual Host em `httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName seu-dominio-ou-ip
    DocumentRoot "C:/inetpub/wwwroot/juridico/public"

    <Directory "C:/inetpub/wwwroot/juridico/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Certifique-se de que `mod_rewrite` está habilitado no `httpd.conf`:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

4. Verifique que o arquivo `.htaccess` existe em `public/` (gerado pelo Laravel por padrão).

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
| Permissão negada | IIS sem escrita em `storage/` | Rever passo 5.8 |
| `php_openssl` faltando | Extensão não habilitada no `php.ini` | Habilitar `extension=openssl` |
| Rewrite não funciona no IIS | Módulo URL Rewrite não instalado | Instalar URL Rewrite Module |
| Erro de migration | Credenciais do banco incorretas | Revisar `.env` seção `DB_*` |

---

## 10. Atualizações Futuras

Para aplicar uma nova versão do sistema:

```cmd
cd C:\inetpub\wwwroot\juridico

git pull origin main

composer install --no-dev --optimize-autoloader
npm install
npm run build

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
```
