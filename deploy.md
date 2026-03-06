# Guia Completo de Deploy --- Laravel + Livewire (cPanel / Shared Hosting)

Este guia mostra como hospedar, atualizar e manter uma aplicação
**Laravel + Livewire** de forma segura e profissional.

------------------------------------------------------------------------

# Estrutura recomendada no servidor

    /home/USER/domains/seu-dominio/
       ├── espaco-islamico/   ← projeto Laravel (git clone aqui)
       └── public_html/       ← apenas arquivos públicos

⚠️ **Nunca coloque o projeto inteiro dentro de `public_html`**

------------------------------------------------------------------------

# 1. Primeira instalação (deploy inicial)

## Entrar na pasta do domínio

``` bash
cd domains/espacoislamico.com/espaco-islamico
```

## Clonar o projeto

``` bash
git clone SEU_REPO_GITHUB espaco-islamico
cd espaco-islamico
```

------------------------------------------------------------------------

# 2. Instalar dependências

## PHP (produção)

``` bash
composer install --no-dev --optimize-autoloader
```

## Node

``` bash
npm ci
npm run build
```

Opcional (economizar espaço):

``` bash
rm -rf node_modules
```

------------------------------------------------------------------------

# 3. Configurar Laravel

``` bash
cp .env.example .env
php artisan key:generate
php artisan migrate --force
```

------------------------------------------------------------------------

# 4. Configurar public_html (IMPORTANTE)

## Limpar pasta pública

``` bash
rm -rf ../public_html/*
```

## Copiar apenas o conteúdo da pasta public

``` bash
cp -r public/* ../public_html/
```

------------------------------------------------------------------------

# 5. Ajustar index.php

Edite:

    public_html/index.php

Substitua:

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

# 6. Garantir .htaccess

Copie:

``` bash
cp public/.htaccess ../public_html/
```

Conteúdo esperado:

    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
    </IfModule>

------------------------------------------------------------------------

# 7. Permissões

``` bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

------------------------------------------------------------------------

# 8. Otimizações (produção)

``` bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

------------------------------------------------------------------------

# ✅ Deploy de atualizações futuras

Sempre que atualizar o código:

``` bash
cd espaco-islamico

git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force
php artisan optimize
```

Se alterou algo em public:

``` bash
cp -r public/* ../public_html/
```

------------------------------------------------------------------------

# Manutenção recomendada

## Limpar caches

``` bash
php artisan optimize:clear
```

## Storage link (se usar uploads)

``` bash
php artisan storage:link
```

## Logs

    storage/logs/laravel.log

## Reiniciar permissões

``` bash
chmod -R 755 storage bootstrap/cache
```

------------------------------------------------------------------------

# Boas práticas

✔ Nunca subir vendor/node_modules para GitHub\
✔ Usar composer --no-dev em produção\
✔ Sempre rodar optimize\
✔ Manter .env fora do public_html\
✔ Fazer backup do banco antes de migrations importantes

------------------------------------------------------------------------

# Comandos rápidos (resumo)

## Primeiro deploy

    git clone
    composer install --no-dev
    npm ci && npm run build
    cp public/* public_html/
    php artisan optimize

## Atualização

    git pull
    composer install --no-dev
    npm run build
    php artisan optimize

------------------------------------------------------------------------

Pronto. Sua aplicação estará segura, leve e pronta para produção.
