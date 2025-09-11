@extends('adminlte::page')

@section('title', __(config('app.name')) . ' ' . 'Menus')

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <style>
        .menu-container {
            min-height: 400px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .menu-item {
            padding: 8px 12px;
            margin: 4px 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: move;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .menu-item:hover {
            background-color: #e9ecef;
        }
        .menu-item i {
            margin-right: 2px;
            width: 20px;
            text-align: center;
        }
        .menu-item .actions {
            opacity: 0.3;
            transition: opacity 0.3s;
        }
        .menu-item:hover .actions {
            opacity: 1;
        }
        .role-card {
            transition: all 0.3s;
        }
        .role-card.active {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .nestable-list {
            padding-left: 30px;
        }
        .badge-menu-count {
            font-size: 0.8em;
        }
        .drag-handle {
            cursor: move;
            margin-right: 10px;
            color: #6c757d;
        }
    </style>
    
@stop

{{-- Extend and customize the browser title --}}
@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop 

@section('content_header')
    <div class="row mb-2">
        <div class="m-0 text-dark col-sm-6">
            <h1 class="m-0 text-dark"></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/home">Início</a></li>
                <li class="breadcrumb-item ">Administração</li>
                <li class="breadcrumb-item active">Menus</li>
            </ol>
        </div>
    </div>    
@stop

@section('content')

    <!-- General Container -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8 text-left h5"><b>Administração de Menus</b></div>
                                                
                            <!-- buttons area -->
                            <div class="col-md-4 text-right">
                                <button id="btnRefresh" class="btnRefresh btn btn-default btn-sm" data-toggle="tooltip" title="{{ __('acl.crud.btnRefreshTip') }}"><i class="fas fa-fw fa-redo"></i> {{ __('acl.crud.btnRefresh') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- filters area -->
                    <div class="card-header">
                        <div class="row mb-0">
                            <div class="col-md-12">
                                <div class="form-group row mb-0">
                                    <label class="col-form-label col-md-3 text-md-right" for="systemSelect">Selecione o Sistema</label>
                                    <div class="col-md-6">
                                        <select id="systemSelect" name="systemSelect" class="form-control selectpicker" data-live-search="true" data-style="form-control" data-toggle="tooltip" title="Selecione o Sistema do qual deseja Administrar os Menus">
                                        <option value=""> Seleção é obrigatória </option>                                            
                                        @foreach($systems as $system)
                                            <option value="{{ $system->id }}">{{ $system->name }}</option>
                                        @endforeach                                            
                                        </select>
                                        <p class="text-muted mb-0">Somente os Sistemas concedidos ao Usuário logado estão na lista.</p>
                                    </div>                                    
                                </div>
                            </div>
                        </div>                    
                    </div>                

                    <div class="card-body">
                        <div class="row">

                            <!-- Todos os Menus Disponíveis -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <label for="roleSelect" class="form-label" style="margin-bottom: 11px;">Ações</label>
                                        <h5 class="mb-0">
                                            <button id="openNewMenuModal" type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createMenuModal" data-toggle="tooltip" title="Clique para Criar um Novo Item de Menu">
                                                <i class="fas fa-plus-circle me-2"></i> Criar Novo Menu
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-list me-2"></i> Menus Disponíveis
                                            <span class="badge bg-light text-dark badge-menu-count"></span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" id="searchMenu" placeholder="Buscar menu..." onkeyup="filterMenus()" data-toggle="tooltip" title="Digite as inícias para filtrar os Itens de Menu">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="mb-4 d-flex justify-content-end">
                                                    <button id="btnSaveMenuPaiOrder" class="btn btn-sm btn-info" data-toggle="tooltip" title="Digite as Salvar a nova Ordem dos Itens de Menu">
                                                        <i class="fas fa-save me-2"></i> Salvar Alterações
                                                    </button>
                                                </div>                                            
                                            </div>
                                        </div>
                                        <div class="menu-container" id="availableMenus">
                                            <!-- Montado dinamicamente AJAX -->
                                        </div>
                                        <div class="mt-2 d-flex justify-content-end">
                                            <button id="btnSaveMenuPaiOrder" class="btn btn-sm btn-info" data-toggle="tooltip" title="Digite as Salvar a nova Ordem dos Itens de Menu">
                                                <i class="fas fa-save me-2"></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Menus do Perfil Selecionado -->
                            <div class="col-md-6">

                                <div class="card">
                                    <div class="card-header">
                                        <label for="roleSelect" class="form-label">Selecione um Perfil:</label>
                                        <select class="form-control" id="roleSelect" onchange="loadRoleMenus(this.value)" data-toggle="tooltip" title="Selecione o Perfil de Acesso para ver os Itens de Menu a ele concedidos">
                                            <option value=""> Selecione </option>
                                        </select>                                    
                                    </div>                                    
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-check-circle me-2"></i> 
                                            Menus do Perfil
                                            <span class="badge bg-light text-dark badge-menu-count" id="assignedCount">0</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="selectedRoleInfo" class="alert alert-info d-none">
                                            Selecione um perfil à esquerda para gerenciar seus menus.
                                        </div>
                                        <div id="roleMenusContainer">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <p class="text-muted">Arraste os menus da esquerda para cá e ordene arrastando e soltando. Por último salve.</p>
                                                </div>
                                                <div class="col-sm-4">
                                                <div class="mb-4 d-flex justify-content-end">
                                                        <button class="btn btn-sm btn-success btnSaveMenuOrder" data-toggle="tooltip" title="Digite as Salvar a nova Ordem dos Itens de Menu">
                                                            <i class="fas fa-save me-2"></i> Salvar Alterações
                                                        </button>                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="menu-container" id="roleMenus">
                                                <!-- Menus serão carregados via AJAX -->
                                            </div>
                                            <div class="mt-2 d-flex justify-content-end">
                                                <button class="btn btn-sm btn-success btnSaveMenuOrder" data-toggle="tooltip" title="Digite as Salvar a nova Ordem dos Itens de Menu">
                                                    <i class="fas fa-save me-2"></i> Salvar Alterações
                                                </button>
                                                <!-- <button class="btn btn-sm btn-outline-secondary" onclick="resetMenuOrder()">
                                                    <i class="fas fa-undo me-2"></i> Reverter
                                                </button> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Menu -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Editar Menu</b></h5>
                    <button type="button" class="close btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#msgOperacaoExcluir').text('');$('#editMenuModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editMenuForm">
                        <input type="hidden" id="editMenuId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Sistema a que pertence o Item de Menu</label>
                                <select class="form-control selectpicker" id="editSystemParent" name="system_id">                                  
                                </select>
                                <div id="error-system_id" class="error invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Menu Pai</label>
                                <select class="form-control selectpicker" id="editMenuParent" name="menu_id">
                                    <option value="">-- Menu Principal (sem pai) --</option>                                    
                                </select>
                                <div id="error-menu_id" class="error invalid-feedback" style="display: none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome do Menu</label>
                                <input type="text" class="form-control" id="editMenuName" name="name" required>
                                <div id="error-name" class="error invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Ícone (opcional)</label>
                                <input type="text" class="form-control" id="editMenuIcon" name="icon" placeholder="Ex: cil-speedometer">
                                <div id="error-icon" class="error invalid-feedback" style="display: none;"></div>                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rota</label>
                                <input type="text" class="form-control" id="editMenuRoute" name="route" placeholder="Ex: /dashboard">
                                
                                <select class="form-control selectpicker" id="editRoute" name="route_id">
                                </select>

                                <div id="error-route" class="error invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Posição</label>
                                <input type="number" class="form-control" id="editMenuPosition" name="position" min="0" value="0">
                                <div id="error-position" class="error invalid-feedback" style="display: none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="active" name="active">
                                    <option value="Y" selected>Ativo</option>
                                    <option value="N">Inativo</option>
                                </select>
                                <div id="error-active" class="error invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnDeleteMenu" data-menu-id="" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="$('#editMenuModal').modal('hide');">Cancelar</button>
                    <button type="button" id="insertMenuModal" class="btn btn-success">Inserir</button>
                    <button type="button" id="updateMenuModal" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal para exibir Alertas necessários -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">            
            <div class="modal-content">
                <div class="modal-header alert-warning">
                    <h4 class="modal-title">Alerta</h4>
                    <button type="button" class="close btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#alertModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#alertModal').modal('hide');">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Container dos toasts -->
    <div id="toastContainer" class="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
    </div>

@stop

{{-- Add common Javascript/Jquery code --}}
@push('js')

    <!-- DataTables JS -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>

        // ativa o tooltip nas páginas
        $('body').tooltip({ selector: '[data-toggle="tooltip"]'});
        
        // configura os Modais para terem seu conteúdo limpo ao serem fechados (hide)
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');
            $('.invalid-feedback').text('').hide();     // clean fields errors messages
            $('#formEntity').trigger("reset");          // clean data fields from form
        });  

        // Mostra TOAST 6 segundos de confirmaçãoes ou alertas
        function showToast(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
            
            const toast = $(`
                <div class="toast ${bgColor} text-white" role="alert" data-delay="5000">
                    <div class="toast-body">
                        <button type="button" class="close text-white mr-2" data-dismiss="toast">
                            <span>&times;</span>
                        </button>
                        ${message}
                    </div>
                </div>
            `);
            
            $('#toastContainer').append(toast);
            toast.toast('show');
            
            // Remove após fechar
            toast.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }        

        // Mostra Modal com alertas que interromperam o fluxo do programa
        function showAlert(message, type = 'error') {
            $('#alertModal .modal-body').html(message);
            $('#alertModal .modal-header')
                .removeClass('alert-success alert-warning alert-danger')
                .addClass(type === 'success' ? 'alert-success' : 
                        type === 'warning' ? 'alert-warning' : 'alert-danger');
            $('#alertModal').modal('show');
        }        

        // Carrega os menus do perfil selecionado
        function loadRoleMenus(roleId) {
            
            // Mostrar loading
            document.getElementById('roleMenus').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';
            
            // AJAX para carregar menus do perfil
            $.ajax({
                url: '/menu/listRoleMenus/' + roleId,
                method: 'GET',
                success: function(response) {
                    let html = renderRoleMenus(response);
                    $('#roleMenus').html(html);
                },
                error: function(error) {
                    showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                }
            });
        }

        // Monta a lista de Menus do Perfil de Acesso
        function renderRoleMenus(menus) {

            if (!Array.isArray(menus)) {
                console.warn("Dados inválidos recebidos em renderRoleMenus:", menus);
                return '<p>Erro ao carregar menus do perfil. Devia ser um Array</p>';
            }            

            // console.log(Array.isArray(menus)); // Deve retornar true
            // console.log(menus); // Veja o que vem na resposta

            // Ordena menus pela posição do pivot
            menus.sort((a, b) => a.pivot.position - b.pivot.position);

            let html = '';
            // onclick="removeMenuFromRole(${menu.id})"

            menus.forEach(menu => {

                // TODO - 
                // o menu não tem a position no pivot para mostrar a ordem
                // recuperar isso e por nu lugar de menu.pivot.profile_id
                // console.log(menu);

                html += `
                    <div class="menu-item" data-menu-id="${menu.id}">
                        <div>
                            <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                            ${menu.icon ? `<i class="${menu.icon}"></i>` : ''}
                            ${menu.name}
                            ${menu.route ? `<small class="text-muted">(${menu.route})</small>` : ''}
                            <br/> ${menu.id ? `<span style="text-indent: 1.8cm;display: inline-block;"><small class="text-muted">Filho de ${menu.menu_id ?? '0'} > Posição ${menu.pivot.profile_id}</small></span>` : ''}
                        </div>
                        <div class="actions">
                            <button class="btn btn-sm btn-outline-danger btnRemoveMenuFromRole" data-menu-id="${menu.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;

                if (menu.children && menu.children.length > 0) {
                    // Ordena filhos também pela posição
                    menu.children.sort((a, b) => a.pivot.position - b.pivot.position);

                    html += '<div class="nestable-list">';
                    menu.children.forEach(child => {
                        html += `
                            <div class="menu-item" data-menu-id="${child.id}">
                                <div>
                                    <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                                    ${child.icon ? `<i class="${child.icon}"></i>` : ''}
                                    ${child.name}
                                    ${child.route ? `<small class="text-muted">(${child.route})</small>` : ''}
                                </div>
                                <div class="actions">
                                    <button class="btn btn-sm btn-outline-danger" >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }
            });

            return html;
        }

        // Abrir modal para editar menu
        function editMenu(menuId, systemParentId, menuParentId, menuName, menuIcon, menuRoute, menuPosition, menuActive) {

            $("#editMenuForm #editMenuId").val(menuId);

            $("#editMenuForm #editSystemParent").val(systemParentId);
            $("#editMenuForm #editMenuParent").val(menuParentId);

            $("#editMenuForm #editMenuName").val(menuName);
            $("#editMenuForm #editMenuIcon").val(menuIcon);
            $("#editMenuForm #editMenuRoute").val(menuRoute);
            $("#editMenuForm #editMenuPosition").val(menuPosition);
            $("#editMenuForm #editMenuActive").val(menuActive);
            
            // Abrir modal
            $('#insertMenuModal').hide();
            $('#btnDeleteMenu').show();
            $('#updateMenuModal').show();
            $('#editMenuModal').modal('show');
        }    

        // Filtrar menus na busca
        function filterMenus() {
            const searchText = document.getElementById('searchMenu').value.toLowerCase();
            const menuItems = document.querySelectorAll('#availableMenus .menu-item');
            
            menuItems.forEach(item => {
                const menuText = item.textContent.toLowerCase();
                if (menuText.includes(searchText)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Após o Documento pronto
        $(document).ready(function() {

            // $('.selectpicker').selectpicker();            

            // Inicializar Sortable para o container de menus disponíveis
            new Sortable($('#availableMenus')[0], {
                group: {
                    name: 'menus',
                    pull: 'clone', // Clona o item ao arrastar
                    put: false // Não permite soltar itens aqui
                },
                animation: 150,

                    sort: true, // Permite ordenação interna
                    // filter: '.no-drag', // Seletor para itens que não podem ser arrastados
                    // draggable: '.menu-item', // Especifica quais elementos são arrastáveis

                onEnd: function(evt) {
                    if (evt.to.id === 'roleMenus') {
                        updateAssignedCount();
                        updateMenuHierarchy();
                    }
                }
            });
            
            // Inicializar Sortable para o container de menus atribuídos
            new Sortable($('#roleMenus')[0], {
                group: 'menus',
                animation: 150,
                onEnd: function(evt) {
                    updateMenuHierarchy();
                }
            });

            // Função para atualizar contagem
            function updateAssignedCount() {
                const count = $('#roleMenus .menu-item').length;
                $('#assignedCount').text(count);
            }

            // Função para atualizar hierarquia (exemplo)
            function updateMenuHierarchy() {
                const menuOrder = [];
                $('#roleMenus .menu-item').each(function(index) {
                    menuOrder.push({
                        id: $(this).data('id'),
                        order: index + 1,
                        menu_id: $(this).data('menu-id')
                    });
                });
            }

            // Eventos de clique para botões (se necessário)
            $(document).on('click', '.btn-remove', function() {
                $(this).closest('.menu-item').remove();
                updateAssignedCount();
                updateMenuHierarchy();
            });

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                statusCode: { 401: function() { window.location.href = "/login"; } }
            });

            $('#openNewMenuModal').on("click", function (e) {
                e.stopImmediatePropagation();

                $('#editMenuForm').trigger("reset");
                $('#btnDeleteMenu').hide();
                $('#insertMenuModal').show();
                $('#updateMenuModal').hide();
                $('#editMenuModal').modal('show');
            });

            // Salva o Menu editado
            $('#updateMenuModal').on('click', function(e) {
                e.stopImmediatePropagation();

                const formData = new FormData(document.getElementById('editMenuForm'));

                $.ajax({
                    url: '/menu/update',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editMenuModal').modal('hide');
                            $('#btnRefresh').trigger("click");
                            showToast(response.message, 'success');
                        } 
                    },
                    error: function(error) {
                        if (error.status === 422) {
                            const errors = error.responseJSON.errors;

                            $("#editMenuForm .invalid-feedback").text('').hide();
                            $.each( error.responseJSON.errors, function( key, value ) {
                                $("#editMenuForm #error-" + key ).text(value).show(); 
                            });                            
                        } else {
                            showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                        }
                    }
                });                
            });

            // Insere o Novo Menu
            $('#insertMenuModal').on('click', function(e) {
                e.stopImmediatePropagation();

                const formData = new FormData(document.getElementById('editMenuForm'));

                $.ajax({
                    url: '/menu/store',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editMenuModal').modal('hide');
                            $('#btnRefresh').trigger("click");
                            showToast(response.message, 'success');
                        } 
                    },
                    error: function(error) {
                        if (error.status === 422) {
                            const errors = error.responseJSON.errors;

                            $("#editMenuForm .invalid-feedback").text('').hide();
                            $.each( error.responseJSON.errors, function( key, value ) {
                                $("#editMenuForm #error-" + key ).text(value).show(); 
                            });                            
                        } else {
                            showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                        }
                    }
                });                
            });

            // Delete Menu
            $('#btnDeleteMenu').on('click', function(e) {
                e.stopImmediatePropagation();

                const menuId = $("#editMenuForm #editMenuId").val();

                $.ajax({
                    url: '/menu/destroy',
                    method: 'POST',
                    data: { "id": menuId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editMenuModal').modal('hide');
                            $('#btnRefresh').trigger("click");
                            showToast(response.message, 'success');
                        } 
                    },
                    error: function(error) {
                        showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                    }
                });                
            });

            // Delete Menu From Role
            // com a sintaxe abaixo captura evento mesmo de elemento criado em tempo de execução
            $(document).on('click', '.btnRemoveMenuFromRole', function(e) {
                e.stopImmediatePropagation();

                const menuId = $(this).data('menu-id');
                const currentRoleId = $('#roleSelect').val();
                // alert('currentRoleId ' + currentRoleId);

                if (!currentRoleId) {
                    showToast('Necessário selecionar um Perfil de Acesso.', 'warning');
                    return;
                }

                $.ajax({
                    url: '/menu/removeMenuFromRole/' + currentRoleId,
                    method: 'DELETE',
                    data: { "menuId": menuId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            loadRoleMenus(currentRoleId);
                            showToast(response.message, 'success');
                        }
                    },
                    error: function(error) {
                        showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                    }
                });                
            });

            // Salva os Menus no Perfil de Acesso depois de Ordenados
            $(document).on('click', '#btnSaveMenuPaiOrder', function(e) {
                e.stopImmediatePropagation();

                let systemId = $('#systemSelect').val();

                // Monta array com a ordenação atual para enviar ao backend (menuOrder)
                const menuOrder = [];
                $('#availableMenus .menu-item').each(function(index) {
                    menuOrder.push({
                        id: $(this).data('menu-id'),
                        position: index + 1
                    });
                });                

                if (menuOrder.length === 0) {
                    showToast('NECESSÁRIO primeiro selecionar um Sistema.', 'warning');
                    $('#systemSelect').focus();
                    return;
                }

                $.ajax({
                    url: '/menu/saveMenusOrder/' + systemId,
                    method: 'POST',
                    data: { menus: menuOrder },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#btnRefresh').trigger("click");
                            showToast(response.message, 'success');
                        }
                    },
                    error: function(error) {
                        showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                    }
                });                
            });

            // Salva os Menus no Perfil de Acesso depois de Ordenados
            $(document).on('click', '.btnSaveMenuOrder', function(e) {
                e.stopImmediatePropagation();

                const currentRoleId = $('#roleSelect').val();

                if (!currentRoleId) {
                    showToast('Necessário selecionar um Perfil de Acesso.', 'warning');
                    $('#roleSelect').focus();
                    return;
                }

                // Monta array com a ordenação atual no formato correto para o sync a ser realizado no  backend (menuOrder)
                const syncData = {};
                $('#roleMenus .menu-item').each(function(index) {
                    const menuId = $(this).data('menu-id');
                    syncData[menuId] = { position: index + 1 };
                });                

                if (syncData.length === 0) {
                    alert('Primeiro inclua os Menus.');
                    return;
                }

                $.ajax({
                    url: '/menu/saveRoleMenus/' + currentRoleId,
                    method: 'POST',
                    data: { menus: syncData },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            loadRoleMenus(currentRoleId);
                            showToast(response.message, 'success');
                        }
                    },
                    error: function(error) {
                        showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                    }
                });     

                // Correspondente ao Ajax acima com axios e como instalar no Laravel 11
                // Ver solução no Deepseek: Laravel Blade FormData Error Solutions
                // axios.post('/menu/saveRoleMenus/' + currentRoleId, { menus: syncData })
                //     .then(function(response) {
                //         if (response.data.success) {
                //             loadRoleMenus(currentRoleId);
                //             showToast(response.data.message, 'success');
                //         }
                //     })
                //     .catch(function(error) {
                //         showAlert(error.response?.data?.message || 'Erro desconhecido', 'error');
                //     });             
                
                
            });

            // Refresh button action
            $('#btnRefresh').on("click", function (e) {
                e.stopImmediatePropagation();

                systemIdSelected = $('#systemSelect').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('menu.listDados') }}",
                    data: { systemId: systemIdSelected },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#roleMenus').html('<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>');
                    },
                    success: function (response) {

                        // 1. Monta a lista de Menus Disponíveis
                        let menus = response.menus;
                        let html = '';

                        if (!menus || menus.length === 0) {
                            html = '<p>Nenhum menu encontrado.</p>';
                        } else {
                            html = renderMenus(menus, 0); // chamada recursiva inicial
                        }

                        $('#availableMenus').html(html); // atualiza o container com os menus

                        // 2. Monta os Perfis no <select>
                        let profileOptions = '<option value="">Selecione</option>';

                        if (response.profiles && response.profiles.length > 0) {
                            response.profiles.forEach(profile => {
                                profileOptions += `<option value="${profile.id}">${profile.name}</option>`;
                            });
                        }
                        $('#roleSelect').html(profileOptions);

                            // 3. Monta os Systems no <select>
                            let systemOptions = '<option value=""> Seleção é obrigatória </option>';
                            let systemSelectId = $('#systemSelect').val();

                            if (response.systems && response.systems.length > 0) {
                                response.systems.forEach(system => {
                                    // sempre marca selecionado o System corrente
                                    if (system.id == systemSelectId) {
                                        systemOptions += `<option value="${system.id}" selected>${system.name}</option>`;    
                                    }
                                });
                            }
                            $('#editSystemParent').html(systemOptions);

                        // 4. Monta os Menus Pai no <select>
                        let menusPaiOptions = '<option value=""> Menu Principal (sem pai) </option>';

                        if (response.menusPai && response.menusPai.length > 0) {
                            response.menusPai.forEach(menusPai => {
                                menusPaiOptions += `<option value="${menusPai.id}">${menusPai.name}</option>`;
                            });
                        }
                        $('#editMenuParent').html(menusPaiOptions);

                        // 5. Monta as Rotas no <select>
                        let routeOptions = '<option value=""> Selecione uma Rota previamente criada </option>';

                        if (response.routes && response.routes.length > 0) {
                            response.routes.forEach(routes => {
                                routeOptions += `<option value="${routes.id}">${routes.route}</option>`;
                            });
                        }
                        $('#editRoute').html(routeOptions);
                    },
                    error: function(error) {
                        showAlert(error.responseJSON?.message || 'Erro desconhecido', 'error');
                    },
                    complete: function() {
                        $('#roleMenus').empty();
                    }
                });                
            });

            // Função recursiva para renderizar menus e submenus
            function renderMenus(menus, level) {
                let html = '';

                menus.forEach(menu => {
                    html += `
                        <div class="menu-item" data-menu-id="${menu.id}">
                            <div>
                                <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                                ${menu.icon ? `<i class="${menu.icon}"></i>` : ''}
                                ${menu.name}
                                ${menu.route ? `<small class="text-muted">(${menu.route})</small>` : ''}
                                <br/> ${menu.id ? `<span style="text-indent: 1.8cm;display: inline-block;"><small class="text-muted">${menu.id} > Filho de ${menu.menu_id ?? '0'} > Posição ${menu.position}</small></span>` : ''}
                            </div>
                            <div class="actions">
                                <button data-menuid="${menu.id}" class="btn btn-sm btn-outline-primary btnEditMenu" 
                                    onclick="editMenu(${menu.id}, ${menu.system_id}, ${menu.menu_id}, '${escapeJS(menu.name)}', '${escapeJS(menu.icon ?? '')}', '${escapeJS(menu.route ?? '')}', ${menu.position}, '${menu.active}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    `;

                    if (menu.children && menu.children.length > 0) {
                        html += '<div class="nestable-list">';
                        html += renderMenus(menu.children, level + 1); // chamada recursiva
                        html += '</div>';
                    }
                });

                return html;
            }

            // On change System select refresh page data
            $('#systemSelect').on("change", function (e) {
                e.stopImmediatePropagation();

                $('#btnRefresh').trigger("click");
            });

            // Função utilitária para escapar aspas em strings JS
            function escapeJS(str) {
                if (!str) return '';
                return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            }   

            // põe o foco no primeiro campo do modal
            $('body').on('shown.bs.modal', '#editMenuModal', function () {
                $('#editMenuParent').focus();
            })      
            
            // convert active checkbox to 'Y' : 'N'            
            function getAtivoValue() {
                return $('input[id="active"]:checked').val() ? 'Y' : 'N';
            }

            // Dispara o clique automaticamente ao carregar a página
            $('#btnRefresh').trigger("click");

        });   

    </script>      
    
@endpush

{{-- Create a common footer --}}
@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        <a href="{{ config('app.company_url', '#') }}">
            {{ config('app.company_name', 'ACL') }}
        </a>
    </strong>
    {{ config('app.name') }} Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
@stop

{{-- Add common CSS customizations --}}
@push('css')

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap-checkbox-switch.css') }}">

    <style type="text/css">

        /* centraliza conteúdo de coluna do DataTables */
        .dt-center {
            text-align: center !important;
        }       

        /* Aumenta o z-index do segundo modal */
        .modal.fade {
            z-index: 1050; /* z-index padrão do Bootstrap 4 */
        }

        #modal2.modal.fade.show {
            z-index: 1060; /* Maior z-index para garantir que o modal 2 sobreponha o modal 1 */
        }  
        
        .invalid-feedback {
            color: red !important;
            font-weight: bold;
        }

        .toast {
            min-width: 250px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }        

        .toast-body {
            font-size: 16px !important;
        }        

        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-warning { background-color: #fff3cd; color: #856404; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }        

    </style>
@endpush