# Guia de Deploy --- Laravel + Livewire

Este guia descreve o processo completo de **deploy e manutenção** de uma
aplicação Laravel em **servidores cPanel / Shared Hosting**.

------------------------------------------------------------------------

# Estrutura recomendada

Nunca colocar o projecto inteiro dentro de `public_html`.

    /home/USER/domains/seu-dominio/
       ├── espaco-islamico/
       └── public_html/

------------------------------------------------------------------------

# Deploy inicial

Entrar na pasta do domínio:

    cd domains/seu-dominio

Clonar projecto:

    git clone URL_DO_REPOSITORIO espaco-islamico
    cd espaco-islamico

------------------------------------------------------------------------

# Instalar dependências

## PHP

    composer install --no-dev --optimize-autoloader

## Node

    npm ci
    npm run build

Opcional:

    rm -rf node_modules

------------------------------------------------------------------------

# Configurar Laravel

    cp .env.example .env
    php artisan key:generate
    php artisan migrate --force

------------------------------------------------------------------------

# Configurar public_html

Limpar:

    rm -rf ../public_html/*

Copiar:

    cp -r public/* ../public_html/

------------------------------------------------------------------------

# Ajustar index.php

Editar:

    public_html/index.php

Trocar:

``` php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

Por:

``` php
require __DIR__.'/../espaco-islamico/vendor/autoload.php';
$app = require_once __DIR__.'/../espaco-islamico/bootstrap/app.php';
```

------------------------------------------------------------------------

# .htaccess

    cp public/.htaccess ../public_html/

Conteúdo:

    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
    </IfModule>

------------------------------------------------------------------------

# Permissões

    chmod -R 755 storage
    chmod -R 755 bootstrap/cache

------------------------------------------------------------------------

# Otimização

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize

------------------------------------------------------------------------

# Atualizar aplicação

    git pull
    composer install --no-dev --optimize-autoloader
    npm ci
    npm run build
    php artisan migrate --force
    php artisan optimize

Se houver alteração em public:

    cp -r public/* ../public_html/

------------------------------------------------------------------------

# Manutenção

Limpar cache:

    php artisan optimize:clear

Criar storage link:

    php artisan storage:link

Logs:

    storage/logs/laravel.log

------------------------------------------------------------------------

# Boas práticas

-   Nunca subir `vendor` ou `node_modules`
-   Usar `composer install --no-dev`
-   Manter `.env` fora do `public_html`
-   Fazer backup do banco antes de migrations importantes

------------------------------------------------------------------------

Aplicação pronta para produção.
