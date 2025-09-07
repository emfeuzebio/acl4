<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Menus por Perfil - ACL</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
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
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">
                    <i class="fas fa-list-alt me-2"></i>Gestão de Menus por Perfil de Acesso
                </h2>
                
                <!-- Filtro e Seleção de Perfil -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Selecionar Perfil
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="roleSelect" class="form-label">Selecione um Perfil:</label>
                                <select class="form-select" id="roleSelect" onchange="loadRoleMenus(this.value)">
                                    <option value="">-- Selecione um perfil --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Criar novo Menu</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createMenuModal">
                                        <i class="fas fa-plus-circle me-2"></i>Criar Novo Menu
                                    </button>                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Todos os Menus Disponíveis -->
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Menus Disponíveis
                                    <span class="badge bg-light text-dark badge-menu-count">{{ $menus->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="searchMenu" placeholder="Buscar menu..." onkeyup="filterMenus()">
                                </div>
                                <div class="menu-container" id="availableMenus">
                                    @foreach($menus->where('parent_id', null) as $menu)
                                        @include('acl/MenuAdminItem', ['menu' => $menu, 'level' => 0])
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menus do Perfil Selecionado -->
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>Menus do Perfil
                                    <span class="badge bg-light text-dark badge-menu-count" id="assignedCount">0</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="selectedRoleInfo" class="alert alert-info d-none">
                                    Selecione um perfil à esquerda para gerenciar seus menus.
                                </div>
                                <div id="roleMenusContainer" class="d-none">
                                    <p class="text-muted">Arraste os menus da esquerda para aqui ou organize a hierarquia por arrastar e soltar:</p>
                                    <div class="menu-container" id="roleMenus">
                                        <!-- Menus serão carregados via AJAX -->
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-primary" onclick="saveMenuOrder()">
                                            <i class="fas fa-save me-2"></i>Salvar Alterações
                                        </button>
                                        <!-- <button class="btn btn-outline-secondary" onclick="resetMenuOrder()">
                                            <i class="fas fa-undo me-2"></i>Reverter
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

    <!-- Modal para Editar Menu -->
    <div class="modal fade" id="editMenuModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editMenuForm">
                        <input type="hidden" id="editMenuId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Menu Pai (opcional):</label>
                                <select class="form-select" id="editMenuParent" name="parent_id">
                                    <option value="">-- Menu Principal (sem pai) --</option>
                                    @foreach($menus->where('parent_id', null) as $menu)
                                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome do Menu *</label>
                                <input type="text" class="form-control" id="editMenuName" name="name" required>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Ícone (opcional)</label>
                                <input type="text" class="form-control" id="editMenuIcon" name="icon" placeholder="Ex: cil-speedometer">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rota (opcional)</label>
                                <input type="text" class="form-control" id="editMenuRoute" name="route" placeholder="Ex: /dashboard">
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Posição</label>
                                <input type="number" class="form-control" id="editMenuPosition" name="position" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="editMenuActive" name="active">
                                    <option value="Y">Ativo</option>
                                    <option value="N">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="deleteMenu()" id="deleteMenuBtn">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateMenu()">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Criar Novo Menu -->
    <div class="modal fade" id="createMenuModal" tabindex="-1" aria-labelledby="createMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createMenuModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Criar Novo Item de Menu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createMenuForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="menuParent" class="form-label">Menu Pai (opcional):</label>
                                <select class="form-select" id="menuParent" name="menu_id">
                                    <option value="">-- Menu Principal (sem pai) --</option>
                                    @foreach($menus->where('parent_id', null) as $menu)
                                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Deixe em branco para criar um menu principal</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="menuName" class="form-label">Nome do Menu: *</label>
                                <input type="text" class="form-control" id="menuName" name="name" required placeholder="Ex: Dashboard">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="menuIcon" class="form-label">Ícone (opcional):</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                    <input type="text" class="form-control" id="menuIcon" name="icon" placeholder="Ex: cil-speedometer">
                                </div>
                                <div class="form-text">
                                    Use classes do CoreUI Icons (cil-) ou Font Awesome (fas-)
                                    <a href="https://icons.coreui.io/" target="_blank" class="ms-1">Ver ícones</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="menuRoute" class="form-label">Rota (opcional):</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-route"></i></span>
                                    <input type="text" class="form-control" id="menuRoute" name="route" placeholder="Ex: /dashboard">
                                </div>
                                <div class="form-text">Rota correspondente no Vue Router</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="menuPosition" class="form-label">Posição:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sort-numeric-down"></i></span>
                                    <input type="number" class="form-control" id="position" name="position" value="0" min="0">
                                </div>
                                <div class="form-text">Ordem de exibição no menu</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="menuActive" class="form-label">Status:</label>
                                <select class="form-select" id="menuActive" id="active name="active">
                                    <option value="Y">Ativo</option>
                                    <option value="N">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" onclick="resetMenuForm()">
                        <i class="fas fa-undo me-2"></i>Limpar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="createNewMenu()">
                        <i class="fas fa-save me-2"></i>Criar Menu
                    </button>
                </div>
            </div>
        </div>
    </div>    

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentRoleId = null;
        let menuState = {};
        
        // Inicializar Sortable para os containers
        document.addEventListener('DOMContentLoaded', function() {
            new Sortable(document.getElementById('availableMenus'), {
                group: {
                    name: 'menus',
                    // pull: true,            // Permite mover itens internamente
                    // put: true              // Permite soltar itens aqui
                    pull: 'clone',
                    put: false
                },
                animation: 150,
                sort: false,
                onEnd: function(evt) {
                    if (evt.to.id === 'roleMenus') {
                        updateAssignedCount();
                    }
                }
            });
            
            const roleMenusContainer = document.getElementById('roleMenus');
            new Sortable(roleMenusContainer, {
                group: 'menus',
                animation: 150,
                onEnd: function(evt) {
                    updateMenuHierarchy();
                }
            });
        });

        // Submissão do formulário de criação de menu
        document.getElementById('createMenuForm').addEventListener('submit', function(e) {
            e.preventDefault();
            createNewMenu();
        });        
        
        // Carregar menus do perfil selecionado
        function loadRoleMenus(roleId) {
            if (!roleId) {
                document.getElementById('selectedRoleInfo').classList.remove('d-none');
                document.getElementById('roleMenusContainer').classList.add('d-none');
                return;
            }
            
            currentRoleId = roleId;
            
            // Mostrar loading
            document.getElementById('roleMenus').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';
            document.getElementById('selectedRoleInfo').classList.add('d-none');
            document.getElementById('roleMenusContainer').classList.remove('d-none');
            
            // AJAX para carregar menus do perfil
            $.ajax({
                url: '/menu/listRoleMenus/' + roleId,
                method: 'GET',
                success: function(response) {
                    document.getElementById('roleMenus').innerHTML = response.html;
                    updateAssignedCount();
                    updateMenuHierarchy();
                },
                error: function() {
                    alert('Erro ao carregar menus do perfil.');
                }
            });
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
        
        // Atualizar contador de menus atribuídos
        function updateAssignedCount() {
            const count = document.querySelectorAll('#roleMenus .menu-item').length;
            document.getElementById('assignedCount').textContent = count;
        }
        
        // Atualizar hierarquia dos menus
        function updateMenuHierarchy() {
            // Implementar lógica para atualizar a hierarquia visual
        }
        
        // Salvar ordenação dos menus
        function saveMenuOrder() {
            if (!currentRoleId) {
                alert('Selecione um perfil primeiro.');
                return;
            }
            
            const menuOrder = [];
            document.querySelectorAll('#roleMenus .menu-item').forEach(item => {
                menuOrder.push(item.dataset.menuId);
            });
            
            $.ajax({
                url: '/menu/saveRoleMenus/' + currentRoleId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    menus: menuOrder
                },
                success: function(response) {
                    alert('Menus salvos com sucesso!');
                },
                error: function() {
                    alert('Erro ao salvar menus.');
                }
            });
        }
        
        // Criar novo perfil
        function createNewRole() {
            const roleName = document.getElementById('newRoleName').value.trim();
            if (!roleName) {
                alert('Digite um nome para o perfil.');
                return;
            }
            
            $.ajax({
                url: '/admin/roles',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: roleName
                },
                success: function(response) {
                    // Recarregar a página para atualizar a lista de perfis
                    location.reload();
                },
                error: function() {
                    alert('Erro ao criar perfil.');
                }
            });
        }
        
        // Abrir modal para editar menu
        function editMenu(menuId, menuName, menuIcon, menuRoute, menuPosition, menuActive) {
            document.getElementById('editMenuId').value = menuId;
            document.getElementById('editMenuName').value = menuName;
            document.getElementById('editMenuIcon').value = menuIcon || '';
            document.getElementById('editMenuRoute').value = menuRoute || '';
            document.getElementById('editMenuPosition').value = menuPosition || '';
            document.getElementById('editMenuActive').value = menuActive || '';
            
            // Abrir modal
            new bootstrap.Modal(document.getElementById('editMenuModal')).show();
        }
        
        // Excluir menu
        function deleteMenu() {
            const menuId = document.getElementById('editMenuId').value;
            
            $.ajax({
                url: '/menu/destroy/' + menuId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    // Fechar modal e recarregar
                    bootstrap.Modal.getInstance(document.getElementById('editMenuModal')).hide();
                    location.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Captura mensagens de validação do Laravel
                        let errors = xhr.responseJSON.errors;
                        let messages = [];

                        for (let field in errors) {
                            messages.push(errors[field].join(' '));
                        }

                        // alert('Erro de validação:\n' + messages.join('\n'));
                        alert('Erro: ' + xhr.responseJSON.message);
                    } else {
                        alert('Erro ao excluir o Menu.');
                    }
                }
            });
        }
        
        // Atualizar menu
        function updateMenu() {
            const menuId = document.getElementById('editMenuId').value;
            const menuName = document.getElementById('editMenuName').value;
            const menuIcon = document.getElementById('editMenuIcon').value;
            const menuRoute = document.getElementById('editMenuRoute').value;
            const menuPosition = document.getElementById('editMenuPositino').value;
            const menuActive = document.getElementById('editMenuActive').value;
            
            $.ajax({
                url: '/menu/update/' + menuId,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: menuName,
                    icon: menuIcon,
                    route: menuRoute,
                    position: menuPosition,
                    active: menuActive,
                },
                success: function(response) {
                    // Fechar modal e recarregar
                    bootstrap.Modal.getInstance(document.getElementById('createMenuModal')).hide();
                    location.reload();
                },
                error: function() {
                    alert('Erro ao atualizar menu.');
                }
            });
        }
        
        // Remover menu do perfil
        function removeMenuFromRole(menuId) {
            if (!confirm('Remover este menu do perfil?')) return;
            
            $.ajax({
                url: '/menu/removeMenuFromRole/' + currentRoleId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    menuId: menuId
                },
                success: function(response) {
                    document.querySelector(`#roleMenus .menu-item[data-menu-id="${menuId}"]`).remove();
                    updateAssignedCount();
                },
                error: function() {
                    alert('Erro ao remover menu.');
                }
            });
        }

        // Função para criar novo menu
        function createNewMenu() {
            const formData = new FormData(document.getElementById('createMenuForm'));
            
            // Validação básica
            if (!formData.get('name')) {
                alert('Por favor, informe o nome do menu.');
                return;
            }
            
            $.ajax({
                url: '/menu/store',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showAlert('Menu criado com sucesso!', 'success');
                        // resetMenuForm();
                        // Recarrega a página para ver o novo menu
                        bootstrap.Modal.getInstance(document.getElementById('createMenuForm')).hide();
                        location.reload();
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Erro ao criar menu. Status: ' + xhr.status);
                }
            });
        }

        // Função para limpar o formulário
        function resetMenuForm() {
            document.getElementById('createMenuForm').reset();
        }

        // Função para mostrar alertas
        function showAlert(message, type = 'info') {
            // Remove alertas anteriores
            $('.alert-dynamic').remove();
            
            // Cria novo alerta
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show alert-dynamic" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insere no topo da página
            $('.container-fluid').prepend(alertHtml);
            
            // Remove automaticamente após 5 segundos
            setTimeout(() => {
                $('.alert-dynamic').alert('close');
            }, 5000);
        }        
    </script>
</body>
</html>