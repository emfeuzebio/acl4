<?php

// A tradução dos Itens de Menu estão no arquivo: lang/vendor/adminlte/pt-br/menu.php


// array de traduções para pt-BR das Entidades do sistema ACL
return [
    
    // Ações do CRUD dos elementos das View Blade do CRUD
    'crud' => [

        // buttons text
        'btnRefresh' => 'Recarregar',
        'btnRefreshTip' => 'Recarregar dados da tabela (Alt+R)',

        'btnInsertNew' => 'Inserir Novo',
        'btnInsertNewTip' => 'Inserir um novo registro (Alt+N)',

        'btnInsert' => 'Inserir',
        'btnInsertTip' => 'Inserir o registro (Alt+S)',

        'btnEdit' => 'Editar',
        'btnEditTip' => 'Editar o registro atual',

        'btnEditProfile' => 'Perfil',
        'btnEditProfileTip' => 'Editar seu Perfil',

        'btnShow' => 'Ver',
        'btnShowTip' => 'Ver o registro atual',

        'btnSave' => 'Salvar',
        'btnSaveTip' => 'Salvar o registro (Alt+S)',

        'btnDestroy' => 'Excluir',
        'btnDestroyTip' => 'Excluir o registro atual',

        'modalDestroyTitle' => 'Excluir',
        'modalDestroyText' => 'Você está certo que deseja Excluir este registro ID: ',

        'btnCancel' => 'Cancelar',
        'btnCancelTip' => 'Cancelar a operação (Esc ou Alt+C)',

        'btnClose' => 'Cancelar',
        'btnCloseTip' => 'Fechar e Cancelar a operação (Esc ou Alt+C)',

        'btnRevoke' => 'Revogar',
        'btnRevokeTip' => 'Revogar o Token',

        'btnRefreshToken' => 'Renovar',
        'btnRefreshTokenTip' => 'Renovar o Token',

        'columns_data' => [
            'yes' => 'SIM',
            'no' => 'NÃO',
        ],

        // error messages
        'errorMessage1451'  => 'Impossível EXCLUIR porque há registros relacionados. (SQL-1451)',
        'errorMessage1062'  => 'Impossível SALVAR! Registro já existe. (SQL-1062)',
        'errorMessage0000'  => 'Houve um ERRO desconhecido! A Operação foi cancelada.',

        // confirm messages
        'confirmMessageInsert'      => 'Inseriu com sucesso o registro ID: ',
        'confirmMessageSave'        => 'Salvou com sucesso o registro ID: ',
        'confirmMessageDestroy'     => 'Excluiu com sucesso o registro ID: ',
        'confirmMessageOperation'   => 'Operação realizada com sucesso.',
        

        // operations label
        'insertRegLabel' => 'Inserir',
        'editRegLabel' => 'Editar',
        'destroytRegLabel' => 'Excluir',
        'todos' => 'Todos',
        'todes' => 'Todos(as)',
        'selectToFilterTip' => 'Selecione para filtrar',

    ],    

    // Static Form User Profile
    // Entidade Perfil de Acesso
    'userprofile' => [
        'title' => 'Perfil de Usuário',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Administração',
        'breadcrumb3' => 'Perfil de Usuário',
        'table_title' => 'Cadastro de Perfis de Usuário',
        'entityName' => 'Perfils de Usuário',

        'columns' => [

            'id-name' => 'ID',
            'id-tip' => 'Código Identificador do Usuário com no mínimo 6 caracteres',
            'id-placeholder' => '', 

            'name-name' => 'Nome',
            'name-tip' => 'Informe o Nome do Usuário',
            'name-placeholder' => 'Ex.: Clark Kent', 

            'email-name' => 'E-Mail',
            'email-tip' => 'E-Mail é Chave Primária do Usuário e não pode ser alterado',
            'email-placeholder' => 'Ex.: name@mail.com', 

            'description-name' => 'Descrição',
            'description-tip' => 'Informe a Descrição do Usuário',
            'description-placeholder' => 'Ex.: Supervisiona todo a produção', 

        ],        
    ],        

    'dictionary' => [
        'organizations' => 'Organizações',
        'systems' => 'Sistemas',
        'users' => 'Usuários',
        'roles' => 'Perfis de Acesso',
        'entities' => 'Entidades',

        'moreInfor' => 'Mais Informações',
        'listLogins' => 'Lista dos últimos Logins',
        'showAllLogins' => 'Ver Todos Logins',

        'listTokens' => 'Lista de Tokens Ativos e Expirados',
        'graphLastUsers' => 'Gráfico dos Novos Usuários nos Últimos 30 Dias',
    ],

    // Entidade Organização
    'dashboard' => [
        'title' => 'Organizações',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Dashboard v1',
        'table_title' => 'Dashboard',
        'entityName' => 'dashboard',

        'columns' => [

            'user-name' => 'Usuário',
            'token-name' => 'Token', 
            'ip-name' => 'IP', 
            'browser-name' => 'Navegador', 
            'create_at-name' => 'Emitido em', 
            'expire_at-name' => 'Expira em', 
            'status-name' => 'Status', 

            // actions bottons 
            'actions-name' => 'Ações',
            'revoke-name' => 'Revogar',
            'refresh-name' => 'Renovar',
        ],
    ],



    // other buttons APAGAR
    // 'extraButtons' => [
    //     'btnPrint' => 'Imprimir',
    //     'butto21' => 'Bb',
    // ],


    // Entidade Organização
    'organization' => [
        'title' => 'Organizações',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Administração',
        'breadcrumb3' => 'Organizações',
        'table_title' => 'Cadastro de Organizações',
        'entityName' => 'Organização',

        'columns' => [

            'id-name' => 'ID',
            'id-tip' => 'Código Identificador da Organização',
            'id-placeholder' => '', 

            'name-name' => 'Nome',
            'name-tip' => 'Informe o Nome da Organização',
            'name-placeholder' => 'Casas Bahia RJ - Filial Centro', 

            'acronym-name' => 'Sigla', 
            'acronym-tip' => 'Informe a Sigla da Organização',
            'acronym-placeholder' => 'CB-RJ Centro', 

            'description-name' => 'Descrição',
            'description-tip' => 'Informe a Descrição da Organização',
            'description-placeholder' => 'A Lojas Casas Bahia RJ - Filial Centro é filial mais antiga', 

            'active-name' => 'Ativo',
            'active-tip' => 'Marque SIM se a Organização está Ativa',
            'active-placeholder' => '', 

            // actions bottons 
            'actions-name' => 'Ações',
        ],
    ],

    // Entidade Sistema
    'system' => [
        'title' => 'Sistemas',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Administração',
        'breadcrumb3' => 'Sistemas',
        'table_title' => 'Cadastro de Sistemas',
        'entityName' => 'Sistema',

        'columns' => [

            'id-name' => 'ID',
            'id-tip' => 'Código Identificador do Sistema',
            'id-placeholder' => '', 

            'organization_id-name' => 'Organização',
            'organization_id-tip' => 'Informe a Organização a que pertence o Sistema',
            'organization_id-placeholder' => 'First Organization', 
            'organization_id-select' => 'Selecione a Organização', 

            'name-name' => 'Nome',
            'name-tip' => 'Informe o Nome do Sistema',
            'name-placeholder' => 'ACL System', 

            'acronym-name' => 'Sigla', 
            'acronym-tip' => 'Informe a Sigla do Sistema',
            'acronym-placeholder' => 'ACL', 

            'description-name' => 'Descrição',
            'description-tip' => 'Informe a Descrição do Sistema',
            'description-placeholder' => 'Sistema ACL Lista de Controle de Acesso', 

            'active-name' => 'Ativo',
            'active-tip' => 'Marque SIM se o Sistema está Ativo',
            'active-placeholder' => '', 

            // actions bottons 
            'actions-name' => 'Ações',
        ],        
    ],

    // Entidade Entidade
    'entity' => [
        'title' => 'Entidades',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Administração',
        'breadcrumb3' => 'Entidades',
        'table_title' => 'Cadastro de Entidades',
        'entityName' => 'Entidade',
        'filterLabel1' => 'Filtrar pelo Sistema',

        'columns' => [

            'id-name' => 'ID',
            'id-tip' => 'Código Identificador da Entidade',
            'id-placeholder' => '', 

            'system_id-name' => 'Sistema',
            'system_id-tip' => 'Informe o Sistema a que pertence a Entidade',
            'system_id-placeholder' => 'First Sistema', 
            'system_id-select' => 'Selecione o Sistema', 

            'model-name' => 'Model',
            'model-tip' => 'Informe o Nome do Model da Entidade',
            'model-placeholder' => 'ACL Entidade Model', 

            'table-name' => 'Tabela', 
            'table-tip' => 'Informe o nome da Tabela no Banco de Dados',
            'table-placeholder' => 'acl_entities', 

            'description-name' => 'Descrição',
            'description-tip' => 'Informe a Descrição da Entidade',
            'description-placeholder' => 'Veículo - Armazena os veículos ', 

            'active-name' => 'Ativo',
            'active-tip' => 'Marque SIM se a Entidade está Ativa',
            'active-placeholder' => '', 

            // actions bottons 
            'actions-name' => 'Ações',
        ],        
    ],

    // Entidade Ações
    'action' => [
        'title' => 'Ações',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Administração',
        'breadcrumb3' => 'Sistemas',
        'table_title' => 'Cadastro de Ações',
        'entityName' => 'Ações',
        'filterLabel1' => 'Filtrar pela Entidade',

        'columns' => [

            'id-name' => 'ID',
            'id-tip' => 'Código Identificador da Ações',
            'id-placeholder' => '', 

            'entity_id-name' => 'Entidade',
            'entity_id-tip' => 'Informe a Entidade a que pertence o Sistema',
            'entity_id-placeholder' => 'Ex.: List Entities', 
            'entity_id-select' => 'Selecione a Entidade', 

            // 'name-name' => 'Nome',
            // 'name-tip' => 'Informe o Nome do Perfis de Acesso',
            // 'name-placeholder' => 'Ex.: Supervisor de Produção', 

            'action-name' => 'Ação',
            'action-tip' => 'Informe o Nome da Ações',
            'action-placeholder' => 'Ex.: List Entities', 

            'route-name' => 'Rota', 
            'route-tip' => 'Informe a Rota do Ação',
            'route-placeholder' => 'Ex.: entity.index', 

            'description-name' => 'Descrição',
            'description-tip' => 'Informe a Descrição da Ações',
            'description-placeholder' => 'Ex.: List system,s Entities', 

            // actions bottons 
            'actions-name' => 'Ações',
        ],        
    ],

    // Entidade Usuários
    'user' => [
        'title' => 'Usuários',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Administração',
        'breadcrumb3' => 'Usuários',
        'table_title' => 'Cadastro de Usuários',
        'entityName' => 'Usuário',

        'columns' => [

            'id-name' => 'ID',
            'id-tip' => 'Código Identificador do Usuário',
            'id-placeholder' => '', 

            // 'system_id-name' => 'Organização',
            // 'system_id-tip' => 'Informe a Organização a que pertence o Sistema',
            // 'system_id-placeholder' => 'First Organization', 
            // 'system_id-select' => 'Selecione a Organização', 

            'name-name' => 'Nome',
            'name-tip' => 'Informe o Nome do Usuários',
            'name-placeholder' => 'Ex.: Mardos dos Montes', 

            'email-name' => 'E-Mail',
            'email-tip' => 'Informe o E-Mail do Usuários',
            'email-placeholder' => 'Ex.: email@domine.com', 

            'password_confirmation-name' => 'Confirme a Senha',
            'password_confirmation-tip' => 'Confirme a Senha do Usuários com 8 caracteres',
            'password_confirmation-placeholder' => 'Ex.: @)*&SnW3[', 

            'password-name' => 'Senha',
            'password-tip' => 'Informe a Senha do Usuários com 8 caracteres',
            'password-placeholder' => 'Ex.: @)*&SnW3[', 

            'active-name' => 'Ativo',
            'active-tip' => 'Marque SIM se o Usuários está Ativo',
            'active-placeholder' => '', 

            // actions bottons 
            'actions-name' => 'Ações',
        ],        
    ],

    // Entidade Perfil de Acesso
    'profile' => [
        'title' => 'Perfis de Acesso',
        'page_title' => '',
        'page_subtitle' => '',
        'breadcrumb1' => 'Início',
        'breadcrumb2' => 'Administração',
        'breadcrumb3' => 'Sistemas',
        'table_title' => 'Cadastro de Perfis de Acesso',
        'entityName' => 'Perfils de Acesso',
        'filterLabel1' => 'Filtrar pelo Sistema',

        'columns' => [

            'id-name' => 'ID',
            'id-tip' => 'Código Identificador do Perfis de Acesso',
            'id-placeholder' => '', 

            'system_id-name' => 'Organização',
            'system_id-tip' => 'Informe a Organização a que pertence o Sistema',
            'system_id-placeholder' => 'First Organization', 
            'system_id-select' => 'Selecione a Organização', 

            'name-name' => 'Nome',
            'name-tip' => 'Informe o Nome do Perfis de Acesso',
            'name-placeholder' => 'Ex.: Supervisor de Produção', 

            'acronym-name' => 'Sigla', 
            'acronym-tip' => 'Informe a Sigla do Perfis de Acesso',
            'acronym-placeholder' => 'Ex.: Sup Prod', 

            'description-name' => 'Descrição',
            'description-tip' => 'Informe a Descrição do Perfis de Acesso',
            'description-placeholder' => 'Ex.: Supervisiona todo a produção', 

            'entityAuthrizations-name' => 'Entidades e Autorizações',
            'entityAuthrizations-tip' => 'Entidades e Autorizações concedidas ao Perfis de Acesso',
            'entityAuthrizations-placeholder' => 'Ex.: Livros - Listar, Inserir, Editar, Excluir', 

            'active-name' => 'Ativo',
            'active-tip' => 'Marque SIM se o Perfis de Acesso está Ativo',
            'active-placeholder' => '', 

            // actions bottons 
            'actions-name' => 'Ações',
        ],        
    ],

];
