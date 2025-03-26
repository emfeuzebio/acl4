#   1. Instalando o ambiente da Aplicação com Docker
    Requisito: Esteja com o Docker instalado no computador
    Nota: este Docker Container tem: Web Server Nginx, PHP 8.3, MySql, PHPMyAdmin e Redis última versão
    1.1 Entrar na pasta de seus projetos
    1.2 Criar uma pasta para o projeto
    1.3 Clone o repositório do projeto: git clone git@github.com:emfeuzebio/containerPHP.git
    1.4 Acessar a pasta do projeto via terminal
    1.5 Abrir o VSCode via terminal: code .
    1.6 Ativar o Container execundo o docker-compose.yml com o comando: docker-compose up

#   2. Instalando o Laravel
    Requisito: Entrar na pasta do projeto, abrir o VSCode e um termninal dentro dele
    2.1 Executar: composer create-project --prefer-dist laravel/laravel application
    2.2 Entrar na pasta application: cd application
    2.3 Excutar: php artisan key:generate --ansi
    2.4 Abrir o .env e copiar o rash da chave APP_KEY=
    2.5 Colar o rash no arquivo docker-compose.yml no environment do app APP_KEY=
    2.6 Reiniciar o Docker Container

#   3. Configurando a conexão com Banco de Dados no Laravel
    3.1 Abrir o arquivo .env e editar as tags

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=acl
        DB_USERNAME=root
        DB_PASSWORD=root

#   4. Aplicar a configurações no Container Docker
    4.1 Desativar o container: docker-compose down
    4.2 Ativar o container: docker-compose up

#   5. Acessar a Aplicação Laravel
    5.1 http://localhost:11000
        
#   6. Acessar o PHPMyAdmin 
    5.1 http://localhost:11001
        Servidor:: db
        Usuário: root
        Senha: root

#   7. Criar o Banco de Dados e as tabelas usando Migrates
    Acompanhar: http://localhost:11001 
    7.1 cd application
    7.2 Criar o Banco de dados e as tabelas básicas necessárias ao Laravel 
        via Migrates: php artisan migrate
    7.3 Ver o status das migrates: php artisan migrate:status
    7.4 Se criar ou mudar alguma Migrate aplique com: php artisan migrate        

#   8. Instalar o Admin LTE versão 3 que usa o Bootstrap 4
    8.1 composer require jeroennoten/laravel-adminlte
    8.2 php artisan adminlte:install
    8.3 composer require laravel/ui                         # bootstrap
    8.4 php artisan ui bootstrap --auth                     # login authentication yes
    8.5 php artisan adminlte:install --only=auth_views      # yes
    8.6 npm install && npm run dev
    8.7 php artisan adminlte:install --type=full --with=main_views
    8.8 npm run build                                       # criar o manifeste e rodar sem o vite ativo
    8.9 Alterar a view Home para a cara do AdminLTE
        Abrir resources/views/home.blade.php e substituir todo o conteúdo pelo abaixo:

            @extends('adminlte::page')

            @section('title', 'Dashboard')

            @section('content_header')
                <h1>Dashboard</h1>
            @stop

            @section('content')
                <p>Welcome to this beautiful admin panel.</p>
            @stop

            @section('css')
                {{-- Add here extra stylesheets --}}
                {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
            @stop

            @section('js')
                <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
            @stop        

    PRONTO! Admin LTE instalado!

#   9 Personalizar a instalação do AdminLTE
    php artisan adminlte:plugins
    php artisan adminlte:plugins install                        # instala todos
    php artisan adminlte:plugins install  --plugin=datatables   # somente este

#   10 Tradução para Português do Laravel e do AdminLTE
    php artisan lang:publish
    composer require lucascudo/laravel-pt-br-localization --dev
    php artisan vendor:publish --tag=laravel-pt-br-localization

    10.1 Aplicar as configurações
        Altere o arquivo config/app.php 
            Linha 73 - timezone: 'timezone' => 'America/Sao_Paulo'
            Linha 86 - mensagens de erro das validações de form: 'locale' => 'pt-br',
            Linha 99: 'fallback_locale' => 'pt-BR',	
            Caso precise personalizar palavras que não foram traduzidas acesse: /application/lang/vendor/pt-br

    10.2 Tradução global dos DataTables para pt-BR
        Copiar o arquivo: DataTables.pt_BR.json para a pasta public/vendor/datatables
        Colar na blade de mais elevada hierarquia após carregar o jquery.dataTables.js o código abaixo na @section('js') 

        <script>
            $(document).ready(function() {
                // traduz todos DataTables
                $.extend(true, $.fn.dataTable.defaults, {
                    language: {
                        url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}"
                    }
                });   
            });   

            // ativa o tooltip nas páginas
            $('body').tooltip({ selector: '[data-toggle="tooltip"]'});

            // configura os Modais para terem seu conteúdo limpo ao serem fechados (hide)
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
                $('.invalid-feedback').text('').hide();
                // alert('Fechou Modal');
            });            

        </script>

    10.3 Tradução específica do AdminLTE
        Abrir o arquivo: config/adminlte.php
        Pesquisar por: 'search' e substituir por 'pesquisar' para traduzir o placerolder dos campos de pesquisa no site


#   11 Usando Vite para Hot Reload
    11.1 Editar o arquivo: config/adminlte.php
         Localizar as entradas abaixo, comentá-las e colar estas abaixo em substituição

            'laravel_asset_bundling' => 'vite',
            'laravel_css_path' => 'resources/css/app.css',
            'laravel_js_path' => 'resources/js/app.js',

    11.2 Editar o arquivo: vite.config.js na raíz da pasta application do projeto
         e deixar o 'input' da forma abaixo

            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],

    11.3 Editar o arquivo: resources/views/vendor/adminlte/master.blade.php
         Localizar as tags: @case('vite')
         Na linha 34 que trata dos .css, adicionar

            @case('vite')
                @vite([config('adminlte.laravel_css_path', 'resources/css/app.css'), config('adminlte.laravel_js_path', 'resources/js/app.js')])

                <!-- EUZ adicionado itens abaixo -->
                <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
                <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
                <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

                @if(config('adminlte.google_fonts.allowed', true))
                    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
                @endif
                <!-- End EUZ -->
            @break

         Na linha 117 que trata dos .js, adicionar

            @case('vite')
            <!-- EUZ adicionado itens abaixo -->
                <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
                <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
                <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
                <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
            <!-- End EUZ -->
            @break       

    11.4 Ativar o Vite no terminal: npm run dev

    11.5 Usando Hot Reload
         Para alterar CSS editar o arquivo: resources/css/app.css
         Para alterar  JS editar o arquivo: resources/css/app.js

         Ex. cole o código abaixo no resources/css/app.css e salve.

            .card {
                background-color: aqua !important; 
            }

         Ao salvar as alterações, automaticamente será feito reload na página




        




#   8. Popular as tabelas usando Seeder
    Acompanhar: http://localhost:10001 
    Nota 1: se precisar criar um seeder: php artisan make:seeder ContaSeeder
    Nota 2: para executar um único seeder: php artisan db:seed ContaSeeder
    8.1 cd application
    8.2 para executar todas as factories e seeders: php artisan db:seed
    PRONTO! Banco de Dados estará populado com os dados mínimos necessários


#   11. Yajara Data Tables
##  Exmplo prático
    https://www.youtube.com/watch?v=N69ZOg59exs&t=191s

##  Documentação
    https://yajrabox.com/docs/laravel-datatables/master/

##  Instalação
    composer require yajra/laravel-datatables:^10.0



##  Para estudo: usar dentro do PHP
{
    // Rollback em todas as migrações e migrar as tabelas novamente
    Artisan::call('migrate:refresh');

    // Alimentar as tabelas
    Artisan::call('db:seed', ['--class' => 'TabelaSeeder']);
}








    







    
#   10. Personalizar a instalação do AdminLTE
    php artisan adminlte:plugins
    php artisan adminlte:plugins install                        # instala todos
    php artisan adminlte:plugins install  --plugin=datatables   # somente este
    php artisan adminlte:plugins remove                         # remove todos
    php artisan adminlte:plugins remove  --plugin=datatables    # remove este











### Yajara Data Tables
# Exmplo prático
https://www.youtube.com/watch?v=N69ZOg59exs&t=191s

# Documentação
https://yajrabox.com/docs/laravel-datatables/master/

# instalação
composer require yajra/laravel-datatables:^10.0




















    

Aguardar montagem das imagens e o container subir


#   2. # Subindo o Docker Container




#   2. Configurando a Aplicação
##  2.1 Configuração do ENV
### 2.1.1 Banco de Dados
    2.1.1.1 MySQL        
    2.1.1.1 MySQL            

#   3. Configurando a Aplicação
##  3.1 Configuração do ENV
### 3.1.1 Banco de Dados
    3.1.1.1 MySQL        
    3.1.1.1 MySQL            



# Subindo o Docker Container



### Instalando o Laravel mais atual

## Preparação
# remova a pasta public da pasta application
# entre na pasta application

# inslale a versão mais atual do Laravel com o comando abaixo
composer create-project --prefer-dist laravel/laravel:^10.0 application

## Configuração
# copiar a chave que foi gerada ao final da instalação continda em APP_KEY do .env
# e colar no arquivo docker-compose.yml em app após volumes: e antes de depends_on:

    environment:
      - COMPOSER_HOME=/composer
      - COMPOSER_ALLOW_SUPERUSER=1
      - APP_ENV=local
      - APP_KEY=base64:xH4BmKDZPZ0pbhpsC+gmmyNor8rf8PzYVkkm1tY6L1w=

# Atualizar a conexão do banco de dados no .env

    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=gpmil
    DB_USERNAME=root
    DB_PASSWORD=root

# caso o não seja gerado o APP_KEY no .env tente instalar o php-curl
sudo apt-get install php-curl

# desmontar o container
docker-compose down

# subir o container
docker-compose up

# acessar a aplicação Laravel
http://localhost:8000/

# acessar o MySQL via phpMyAdmin
http://localhost:8001

login: 
    servidor: db
    user: root
    pwd: root

# criar o banco de dados no MySQL
CREATE DATABASE IF NOT EXISTS gpmil;

# acessar o container [app]
docker compose exec app bash
ou
docker-compose exec app bash

# Pupular as tabelas essenciais do Laravel com php artisan migrate
cd application

php artisan migrate:install

php artisan migrate:status 

php artisan migrate

# falta carregar dados nas tabelas criadas com seeder


#### Instalando o AdminLTE no Laravel
https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Installation
https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Artisan-Console-Commands
no terminal do VsCode pasta application

# 1. Require the package
# On the root folder of your Laravel project, require the package using the composer tool:
composer require jeroennoten/laravel-adminlte

# 2. Install the package resources
# Install the required package resources using the next command:
php artisan adminlte:install

# 3. Install the legacy authentication scaffolding (optional)
composer require laravel/ui
php artisan ui bootstrap --auth
php artisan adminlte:install --only=auth_views
php artisan adminlte:install --only=config --only=main_views
php artisan adminlte:install --with=auth_views --with=basic_routes
php artisan adminlte:install --type=full --with=main_views

# 3.1 Verificar se todos os packages foram instalados
php artisan adminlte:status

# pronto: acesse home Laravel e terá no canto sup dir Log In e Register
apos se registrar, ao fazer login via dar um erro de vite

editar o arquivo
resources/views/layouts/app.blade.php
comentar a linha do Vite acima do </head>
<!-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) -->

### por ver ainda uma forma de ao iniciar o container já fezer o migrate e o seeder dos dados iniciais

# Colocando tradução no AdminLTE
fonte: https://github.com/lucascudo/laravel-pt-BR-localization/tree/master?tab=readme-ov-file

Instalação
Em /application executar os seguinte comandos:

php artisan lang:publish
composer require lucascudo/laravel-pt-br-localization --dev
php artisan vendor:publish --tag=laravel-pt-br-localization

# Configure o Framework para utilizar 'pt-BR' como linguagem padrão
# Altere Linha 86 do arquivo config/app.php para:
'locale' => 'pt_BR',
# Linha 99
'fallback_locale' => 'pt_BR',
# Linha 86 - mensagens de erro das validações de form
# Ajuste também o timezone
# Linha 73
'timezone' => 'America/Sao_Paulo'

# Ajuste conforme o nessessário para aquelas palavras que não foram traduzidas em:
/application/lang/vendor/pt-br


### Yajara Data Tables
# Exmplo prático
https://www.youtube.com/watch?v=N69ZOg59exs&t=191s

# Documentação
https://yajrabox.com/docs/laravel-datatables/master/

# instalação
composer require yajra/laravel-datatables:^10.0


### configurar o Menu do AdminLTE


### Configurar a master.blade do AdminLTE para carregar os css e js necessários ao DataTables
editar o template geral da aplicação em
resources/views/vendor/adminlte/master.blade.php

colocar os arquivos .css necessários 
colocar os arquivos .js necessários 
