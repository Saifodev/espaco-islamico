# Espaço Islâmico --- Plataforma Web

Este projecto é uma aplicação web desenvolvida com **Laravel**,
utilizando também **Node.js**, **Vite** e **MySQL**.

Este guia foi escrito para permitir que **qualquer desenvolvedor consiga
clonar, configurar e executar o projecto localmente** de forma simples.

------------------------------------------------------------------------

# Tecnologias utilizadas

-   PHP **8.2.\***
-   Laravel
-   MySQL
-   Node.js / npm
-   Vite
-   Composer
-   Git

------------------------------------------------------------------------

# Requisitos

Antes de iniciar, certifique-se de que possui os seguintes softwares
instalados:

## Git

https://git-scm.com/

Verificar:

    git --version

## PHP 8.2

    php -v

https://www.php.net/downloads

## Composer

https://getcomposer.org/

    composer --version

## Node.js / npm

https://nodejs.org/

    node -v
    npm -v

## MySQL

https://www.mysql.com/

    mysql --version

------------------------------------------------------------------------

# Clonar o projecto

    git clone URL_DO_REPOSITORIO
    cd nome-do-projeto

------------------------------------------------------------------------

# Instalar dependências

## PHP

    composer install

## Node

    npm install

------------------------------------------------------------------------

# Configurar ambiente

Criar `.env`:

    cp .env.example .env

------------------------------------------------------------------------

# Configurar base de dados

Criar database no MySQL.

Exemplo:

    DB_DATABASE=espaco_islamico
    DB_USERNAME=root
    DB_PASSWORD=

------------------------------------------------------------------------

# Gerar chave Laravel

    php artisan key:generate

------------------------------------------------------------------------

# Executar migrations

    php artisan migrate

Se houver seeders:

    php artisan db:seed

------------------------------------------------------------------------

# Compilar assets

    npm run build

Modo desenvolvimento:

    npm run dev

------------------------------------------------------------------------

# Storage link

    php artisan storage:link

------------------------------------------------------------------------

# Executar servidor

    php artisan serve

A aplicação ficará disponível em:

    http://127.0.0.1:8000

------------------------------------------------------------------------

# Estrutura do projecto

    app/
    bootstrap/
    config/
    database/
    public/
    resources/
    routes/
    storage/
    tests/
    vendor/

------------------------------------------------------------------------

# Boas práticas

-   Nunca subir `.env`
-   Nunca subir `node_modules`
-   Nunca subir `vendor`
-   Sempre rodar migrations após atualizar código
-   Usar `.env` diferente em produção

------------------------------------------------------------------------

# Atualizar projecto

    git pull
    composer install
    npm install
    php artisan migrate
    npm run build

------------------------------------------------------------------------

# Licença

Projeto distribuído sob licença MIT.
