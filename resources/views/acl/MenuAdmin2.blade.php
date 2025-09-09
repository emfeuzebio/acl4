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
            padding: 10px 15px;
            margin: 5px 0;
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
            margin-right: 10px;
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
                            <div class="col-md-3 text-left h5"><b>Administração de Menus</b></div>
                            
                            <!-- message area -->
                            <div class="col-md-4 text-left">
                                <div style="padding: 0px;  background-color: transparent;">
                                    <div id="alert" class="alert alert-danger" style="margin-bottom: 0px; display: none; padding: 2px 5px 2px 5px;">
                                        <a class="close" onClick="$('.alert').hide()">&times;</a>  
                                        <div class="alert-content">Mensagem</div>
                                    </div>
                                </div>                         
                            </div>
                                                
                            <!-- buttons area -->
                            <div class="col-md-5 text-right">

                                <!-- extra buttons from entity config json -->
                                @foreach(optional($entityConfig ?? null)->pageButtons ?? [] as $button)
                                    <button id="{{ $button->btnName }}" class="{{ $button->btnClass }}" data-toggle="tooltip" 
                                        title="{{ $button->btnTitle }}" >{{ $button->btnLabel }}
                                    </button>
                                @endforeach

                                <button id="btnRefresh" class="btnRefresh btn btn-default btn-sm" data-toggle="tooltip" title="{{ __('acl.crud.btnRefreshTip') }}"><i class="fas fa-fw fa-redo"></i> {{ __('acl.crud.btnRefresh') }}</button>
                                <button id="btnInsertNew" style="display: none;" class="btnInsertNew btn btn-success btn-sm" data-toggle="tooltip" title="{{ __('acl.crud.btnInsertNewTip') }}"><i class="fas fa-fw fa-plus"></i> {{ __('acl.crud.btnInsertNew') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- filters area -->
                    <div class="card-header" id="filter_area" style="display: none;">
                        <div class="row">
                            <div id="filterDiv1" class="col-md-3 form-group" style="margin-bottom: 0px; display: none;">
                                <label id="filterLabel1" class="form-label">Filter 1</label>
                                <select id="filterSelect1" name="filterSelect1" class="form-control selectpicker" data-live-search="true" data-style="form-control" data-toggle="tooltip" title="{{ __('acl.crud.selectToFilterTip')}}">
                                    <option value="">{{ __('acl.crud.todes')}}</option>
                                    @foreach($filterOptions1 ?? (object) [] as $option) 
                                    <option value="{{$option->id}}">{{$option->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="filterDiv2" class="col-md-3 form-group" style="margin-bottom: 0px; display: none;">
                                <label id="filterLabel2" class="form-label">Filter 2 </label>
                                <select id="filterSelect2" name="filterSelect2" class="form-control selectpicker" data-live-search="true" data-style="form-control" data-toggle="tooltip" title="{{ __('acl.crud.selectToFilterTip')}}">
                                    <option value="">{{ __('acl.crud.todes')}}</option>
                                    @foreach($filterOptions2 ?? (object) [] as $option) 
                                    <option value="{{$option->id}}">{{$option->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="filterDiv3" class="col-md-3 form-group" style="margin-bottom: 0px; display: none;">
                                <label id="filterLabel3" class="form-label">Filter 3</label>
                                <select id="filterSelect3" name="filterSelect3" class="form-control selectpicker" data-live-search="true" data-style="form-control" data-toggle="tooltip" title="{{ __('acl.crud.selectToFilterTip')}}">
                                    <option value="">{{ __('acl.crud.todes')}}</option>
                                    @foreach($filterOptions3 ?? (object) [] as $option) 
                                    <option value="{{$option->id}}">{{$option->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="filterDiv4" class="col-md-3 form-group" style="margin-bottom: 0px; display: none;">
                                <label id="filterLabel4" class="form-label">Filter 4</label>
                                <select id="filterSelect4" name="filterSelect4" class="form-control selectpicker" data-live-search="true" data-style="form-control" data-toggle="tooltip" title="{{ __('acl.crud.selectToFilterTip')}}">
                                    <option value="">{{ __('acl.crud.todes')}}</option>
                                    <option value="SIM">SIM</option>
                                    <option value="NÃO">NÃO</option>
                                </select>
                            </div>
                        </div>
                    </div>                

                    <div class="card-body">
                        <div class="row">

                            <!-- Todos os Menus Disponíveis -->
                            <div class="col-md-6" style="background-color: grey;">

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <button id="openNewMenuModal" type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#createMenuModal">
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
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="searchMenu" placeholder="Buscar menu..." onkeyup="filterMenus()">
                                        </div>
                                        <div class="menu-container" id="availableMenus">
                                            <!-- Montado dinamicamente AJAX -->
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Menus do Perfil Selecionado -->
                            <div class="col-md-6" style="background-color: grey;">

                                <div class="card">
                                    <div class="card-header">

                                        <label for="roleSelect" class="form-label">Selecione um Perfil:</label>
                                        <select class="form-control" id="roleSelect" onchange="loadRoleMenus(this.value)">
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
                                        <div id="roleMenusContainer" class="d-none">
                                            <p class="text-muted">Arraste os menus da esquerda para cá e organize a hierarquia arrastando e soltando</p>
                                            <div class="menu-container" id="roleMenus">
                                                <!-- Menus serão carregados via AJAX -->
                                            </div>
                                            <div class="mt-3">
                                                <button class="btn btn-sm btn-primary btnSaveMenuOrder">
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
            // alert('Fechou Modal');
        });  

        // Carrega os menus do perfil selecionado
        function loadRoleMenus(roleId) {
            
            if (!roleId) {
                document.getElementById('selectedRoleInfo').classList.remove('d-none');
                document.getElementById('roleMenusContainer').classList.add('d-none');
                return;
            }
            
            // Mostrar loading
            document.getElementById('roleMenus').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';
            document.getElementById('selectedRoleInfo').classList.add('d-none');
            document.getElementById('roleMenusContainer').classList.remove('d-none');
            
            // AJAX para carregar menus do perfil
            $.ajax({
                url: '/menu/listRoleMenus/' + roleId,
                method: 'GET',
                success: function(response) {
                    let html = renderRoleMenus(response);
                    $('#roleMenus').html(html);
                    // document.getElementById('roleMenus').innerHTML = response.html;
                    // updateAssignedCount();
                    // updateMenuModalHierarchy();
                },
                error: function() {
                    alert('Erro ao carregar menus do perfil.');
                }
            });
        }

        // Monta a lista de Menus do Perfil
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
                html += `
                    <div class="menu-item" data-menu-id="${menu.id}">
                        <div>
                            <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                            ${menu.icon ? `<i class="${menu.icon}"></i>` : ''}
                            ${menu.name}
                            ${menu.route ? `<small class="text-muted">(${menu.route})</small>` : ''}
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
                    // onclick="removeMenuFromRole(${child.id})"
                    html += '</div>';
                }
            });

            return html;
        }

        // Abrir modal para editar menu
        function editMenu(menuId, menuName, menuIcon, menuRoute, menuPosition, menuActive) {

            $("#editMenuForm #editMenuId").val(menuId);
            $("#editMenuForm #editMenuParent").val(menuId);
            $("#editMenuForm #editMenuName").val(menuName);
            $("#editMenuForm #editMenuIcon").val(menuIcon);
            $("#editMenuForm #editMenuRoute").val(menuRoute);
            $("#editMenuForm #editMenuPosition").val(menuPosition);
            $("#editMenuForm #editMenuActive").val(menuActive);
            // alert('menuActive: ' + menuActive);
            
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

        // DataTables operações padrão
        $(document).ready(function() {

            // Dispara o clique automaticamente ao carregar a página
            $('#btnRefresh').trigger("click");

                // Inicializar Sortable para o container de menus disponíveis
                new Sortable($('#availableMenus')[0], {
                    group: {
                        name: 'menus',
                        pull: 'clone',
                        put: false
                    },
                    animation: 150,
                    sort: false,
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
                    
                    // Aqui você pode enviar para o servidor
                    console.log('Ordem atualizada:', menuOrder);
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
                            $('#alert .alert-content').html("{{ __('acl.crud.confirmMessageSave') }}" + response.dados.name);
                            $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);

                            $('#editMenuModal').modal('hide');
                            $('#btnRefresh').trigger("click");
                        } else {
                            alert('Erro: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            $("#editMenuForm .invalid-feedback").text('').hide();
                            $.each( xhr.responseJSON.errors, function( key, value ) {
                                $("#editMenuForm #error-" + key ).text(value).show(); 
                            });                            
                        } else {
                            alert('Erro ao criar menu. Status: ' + xhr.status);
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
                            // showAlert('Menu criado com sucesso!', 'success');
                            $('#alert .alert-content').html("{{ __('acl.crud.confirmMessageInsert') }}" + response.dados.name);
                            $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);

                            $('#editMenuModal').modal('hide');
                            $('#btnRefresh').trigger("click");
                        } else {
                            alert('Erro: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            $("#editMenuForm .invalid-feedback").text('').hide();
                            $.each( xhr.responseJSON.errors, function( key, value ) {
                                $("#editMenuForm #error-" + key ).text(value).show(); 
                            });                            
                        } else {
                            alert('Erro ao criar menu. Status: ' + xhr.status);
                        }
                    }
                });                
            });

            // Delete Menu
            $('#btnDeleteMenu').on('click', function(e) {
                e.stopImmediatePropagation();

                const menuId = $("#editMenuForm #editMenuId").val();
                // alert(menuId);

                $.ajax({
                    url: '/menu/destroy',
                    method: 'POST',
                    data: { "id": menuId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#alert .alert-content').html("{{ __('acl.crud.confirmMessageDestroy') }}" + response.dados.name);
                            $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);

                            $('#editMenuModal').modal('hide');
                            $('#btnRefresh').trigger("click");
                        } else {
                            alert('Erro: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.message;
                        alert('Erro ao excluir o menu. Status: ' + xhr.message);
                    }
                });                
            });

            // Delete Menu From Role
            // com a sintaxe abaixo captura evento mesmo de elemento criado em tempo de execução
            $(document).on('click', '.btnRemoveMenuFromRole', function(e) {
                e.stopImmediatePropagation();

                const menuId = $(this).data('menu-id');
                const currentRoleId = $('#roleSelect').val();
                // alert('btnRemoveMenuFromRole ' + menuId);
                // alert('currentRoleId ' + currentRoleId);

                if (!currentRoleId) {
                    alert('Necessário selecionar um Perfil de Acesso.');
                    return;
                }

                $.ajax({
                    url: '/menu/removeMenuFromRole/' + currentRoleId,
                    method: 'DELETE',
                    data: { "menuId": menuId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#alert .alert-content').html("{{ __('acl.crud.confirmMessageDestroy') }}" + menuId);
                            $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);
                            loadRoleMenus(currentRoleId);
                            // $('#btnRefresh').trigger("click");
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.message;
                        alert('Erro ao excluir o Menu do Perfil de Acesso. Status: ' + xhr.message);
                    }
                });                
            });

            // Salva os Menus no Perfil de Acesso depois de Ordenados
            $(document).on('click', '.btnSaveMenuOrder', function(e) {
                e.stopImmediatePropagation();

                const currentRoleId = $('#roleSelect').val();

                if (!currentRoleId) {
                    alert('Necessário selecionar um Perfil de Acesso.');
                    return;
                }

                const menuOrder = [];
                // document.querySelectorAll('#roleMenus .menu-item').forEach(item => {
                //     menuOrder.push(item.dataset.menuId);
                // });
                // console.log(menuOrder);
                $('#roleMenus .menu-item').each(function(index) {
                    // menuOrder.push($(this).data('menu-id'));

                    menuOrder.push({
                        id: $(this).data('menu-id'),
                        position: index + 1  // A posição é baseada na ordem atual (index + 1)
                    });                    
                });  

                if (menuOrder.length === 0) {
                    alert('Primeiro inclua os Menus.');
                    return;
                }

                $.ajax({
                    url: '/menu/saveRoleMenus/' + currentRoleId,
                    method: 'POST',
                    data: { menus: menuOrder },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#alert .alert-content').html("{{ __('acl.crud.confirmMessageSave') }}");
                            $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);
                            loadRoleMenus(currentRoleId);
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.message;
                        alert('Erro ao excluir o Menu do Perfil de Acesso. Status: ' + xhr.message);
                    }
                });                
            });

            // Refresh button action
            $('#btnRefresh').on("click", function (e) {
                e.stopImmediatePropagation();

                // document.getElementById('roleMenus').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';

                $.ajax({
                    type: "GET",
                    url: "{{ route('menu.listDados') }}",
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

                        // 3. Monta os Menus Pai no <select>
                        let menusPaiOptions = '<option value=""> Menu Principal (sem pai) </option>';

                        if (response.menusPai && response.menusPai.length > 0) {
                            response.menusPai.forEach(menusPai => {
                                menusPaiOptions += `<option value="${menusPai.id}">${menusPai.name}</option>`;
                            });
                        }

                        $('#editMenuParent').html(menusPaiOptions);

                    },
                    error: function (error) {
                        console.error('Erro ao carregar os menus:', error);
                        $('#menu-list').html('<p>Erro ao carregar os menus.</p>');
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
                                ${menu.id}
                                ${menu.menu_id}
                                ${menu.position}
                                ${menu.active}
                            </div>
                            <div class="actions">
                                <button data-menuid="${menu.id}" class="btn btn-sm btn-outline-primary btnEditMenu" 
                                    onclick="editMenu(${menu.id}, '${escapeJS(menu.name)}', '${escapeJS(menu.icon ?? '')}', '${escapeJS(menu.route ?? '')}', ${menu.position}, '${menu.active}')">
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

            // Função utilitária para escapar aspas em strings JS
            function escapeJS(str) {
                if (!str) return '';
                return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            }   

            /**
             * põe o foco no primeiro campo do modal
             */
            $('body').on('shown.bs.modal', '#editModal', function () {
                $('#name').focus();
            })      
            
            // convert active checkbox to 'Y' : 'N'            
            function getAtivoValue() {
                return $('input[id="active"]:checked').val() ? 'Y' : 'N';
            }
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


    </style>
@endpush