@extends('layouts.app')

@section('title', __(config('app.name')) . ' ' . __('acl.user.title'))

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('page_title', __('acl.' . request()->path() . '.page_title'))
@section('page_subtitle', __('acl.' . request()->path() . '.page_subtitle'))

@section('breadcrumb1', __('acl.' . request()->path() . '.breadcrumb1'))
@section('breadcrumb2', __('acl.' . request()->path() . '.breadcrumb2'))
@section('breadcrumb3', __('acl.' . request()->path() . '.breadcrumb3'))

@section('content_body')
    @section('table_title', __('acl.' . request()->path() . '.table_title'))
    
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="{{ __('acl.crud.btnCancelTip') }}" onClick="$('#editModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <form id="formEntity" name="formEntity"  action="javascript:void(0)" class="form-horizontal" method="post">

                        <div class="form-group" id="form-group-id">
                            <label class="form-label">{{ __('acl.user.columns.id-name') }}</label>
                            <input class="form-control" value="" type="text" id="id" name="id" placeholder="" readonly data-toggle="tooltip" title="{{ __('acl.user.columns.id-tip') }}">
                        </div>                         
                        <!-- 
                        <div class="form-group">
                            <label class="form-label">{{ __('acl.system.columns.organization_id-name') }}</label>
                            <select name="organization_id" id="organization_id" class="form-control selectpicker" data-style="form-control" data-live-search="true" data-toggle="tooltip" data-placement="top" title="{{ __('acl.system.columns.name-tip') }}">
                                <option value="">{{ __('acl.system.columns.organization_id-select') }}  </option>
                                @foreach( $organizations as $organization )
                                <option value="{{$organization->id}}">{{$organization->acronym}}</option>
                                @endforeach
                            </select>
                            <div id="error-organization_id" class="error invalid-feedback" style="display: none;"></div>
                        </div>                      
                        -->

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.user.columns.name-name') }}</label>
                            <input class="form-control" value="" type="text" id="name" name="name" placeholder="{{ __('acl.user.columns.name-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.user.columns.name-tip') }}" >
                            <div id="error-name" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">{{ __('acl.user.columns.email-name') }}</label>
                            <input class="form-control" value="" type="text" id="email" name="email" placeholder="{{ __('acl.user.columns.email-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.user.columns.email-tip') }}" >
                            <div id="error-email" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.user.columns.phone-name') }}</label>
                            <input class="form-control" value="" type="text" id="phone" name="phone" placeholder="{{ __('acl.user.columns.phone-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.user.columns.phone-tip') }}" >
                            <div id="error-phone" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.user.columns.photo-name') }}</label>

                            <!-- Pré-visualização da imagem: onchange="previewPhoto(event)" -->
                            <div id="photo-preview" class="mt-0" style="display: none;">
                                <img id="preview-image" src="" alt="Preview" class="img-thumbnail" style="max-width: 70px;" />
                                <button type="button" id="btnExcluirFoto" class="btn btn-danger btn-sm mt-2">Remover foto</button>
                            </div>

                            <input
                                class="form-control" type="file"
                                id="photo" name="photo" accept="image/*"
                            />                            

                            <input type="hidden" id="photo-removed" name="photo_removed" value="0" />

                            <div id="error-photo" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                        
                        <!-- 
                        <div class="form-group">
                            <label class="form-label">{{ __('acl.user.columns.password-name') }}</label>
                            <input class="form-control" value="" type="password" id="password" name="password" placeholder="{{ __('acl.user.columns.password-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.user.columns.password-tip') }}" >
                            <div id="error-password" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.user.columns.password_confirmation-name') }}</label>
                            <input class="form-control" value="" type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('acl.user.columns.password-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.user.columns.password-tip') }}" >
                            <div id="error-password_confirmation" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                         -->

                        <div class="form-group input-group-sm">
                            <label class="form-label" data-toggle="tooltip" title="{{ __('acl.user.columns.active-tip') }}">{{ __('acl.user.columns.active-name') }}</label>
                            <label class="switch">
                                <input type="checkbox" id="active" name="active" class="switch-input" data-toggle="tooltip" title="Marcar se  o Perfil de Acesso está Ativo">
                                <span class="switch-label" data-on="{{ __('acl.crud.columns_data.yes') }}" data-off="{{ __('acl.crud.columns_data.no') }}"></span>
                                <span class="switch-handle"></span>
                            </label>
                            <div id="error-active" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                    </form>        

                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <label id="msgOperacao" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-toggle="tooltip" title="{{ __('acl.crud.btnCancelTip') }}" onClick="$('#editModal').modal('hide');">{{ __('acl.crud.btnCancel') }}</button>
                        <button type="button" class="btn btn-primary btnSave" style="display: none;" id="btnSave" data-operation="save" data-toggle="tooltip" title="{{ __('acl.crud.btnSaveTip') }}">{{ __('acl.crud.btnSave') }}</button>
                        <button type="button" class="btn btn-success btnSave" style="display: none;" id="btnInsert" data-operation="insert" data-toggle="tooltip" title="{{ __('acl.crud.btnInsertTip') }}">{{ __('acl.crud.btnInsert') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Grant Organization to User -->
    <div class="modal fade" id="modalGrantOrganization" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantOrganization').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Conceder Organizações</legend>

                                <div class="table-responsive col-md-12">
                                    <table id="tblOrganizationsGranted" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Organização</th>
                                                <th>Concedida ao Usuário</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot></tfoot>                
                                    </table> 
                                </div>
                            </fieldset>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-5 text-left">
                        <label id="msgOperacaoOrganization" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantOrganization').modal('hide');">Fechar</button>
                        <button type="button" class="btn btn-primary btnToggleOrganization" id="btnToggleOrganization" data-user_id="" data-toggle="tooltip" title="{{ __('acl.crud.btnSaveTip') }}">{{ __('acl.crud.btnSave') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Grant System to User -->
    <div class="modal fade" id="modalGrantSystem" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantSystem').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Conceder Sistemas</legend>

                                <div class="table-responsive col-md-12">
                                    <table id="tblSystemsGranted" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Sistema</th>
                                                <th>Concedido ao Usuário</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot></tfoot>                
                                    </table> 
                                </div>
                            </fieldset>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-5 text-left">
                        <label id="msgOperacaoPerfis" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantSystem').modal('hide');">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Grant Profile (Role) to User -->
    <div class="modal fade" id="modalGrantRole" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantRole').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Conceder Perfis de Acesso</legend>

                                <div class="table-responsive col-md-12">
                                    <table id="tblRolesGranted" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Perfil de Acesso</th>
                                                <th>Concedido ao Usuário</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot></tfoot>                
                                    </table> 
                                </div>
                            </fieldset>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-5 text-left">
                        <label id="msgOperacaoPerfis" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantRole').modal('hide');">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalRoleShow" tabindex="-1" role="dialog" aria-labelledby="modalExemploLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detalhes do Perfil de Acesso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="perfil-tab" data-toggle="tab" href="#perfil" role="tab" aria-controls="perfil" aria-selected="true">Detalhes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="autorizacoes-tab" data-toggle="tab" href="#autorizacoes" role="tab" aria-controls="autorizacoes" aria-selected="false">Autorizações</a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content" id="tab-perfil">
                        <div class="tab-pane fade show active" id="perfil" role="tabpanel" aria-labelledby="perfil-tab" style="padding-top: 20px;">

                            <fieldset class="border p-2">
                                <form id="formPerfilEditar" name="formPerfilEditar" action="javascript:void(0)" class="form-horizontal" method="post">

                                    <div class="form-group input-group-sm" id="form-group-id">
                                        <label class="form-label">ID</label>
                                        <input class="form-control" value="" type="text" id="id" name="id" placeholder="" readonly data-toggle="tooltip" title="ID do Perfil de Acesso">
                                    </div>

                                    <div class="form-group input-group-sm">
                                        <label class="form-label">Ação</label>
                                        <input class="form-control" disabled value="" type="text" id="name" name="name" placeholder="" data-toggle="tooltip" title="Informe o Nome do Perfil de Acesso" >
                                        <div id="error-name" class="error invalid-feedback" style="display: none;"></div>
                                    </div>

                                    <div class="form-group input-group-sm">
                                        <label class="form-label">Descrição</label>
                                        <textarea class="form-control" disabled id="description" name="description" placeholder="" data-toggle="tooltip" title="Informe a Descrição do Perfil de Acesso" rows="4"></textarea>
                                        <div id="error-description" class="error invalid-feedback" style="display: none;"></div>
                                    </div>

                                    <div class="form-group input-group-sm">
                                        <label class="form-label" data-toggle="tooltip" title="Marcar se o Perfil de Acesso está Ativo">Ativo</label>
                                        <label class="switch">
                                            <input type="checkbox" disabled id="active" name="active" class="switch-input" data-toggle="tooltip" title="Marcar se  o Perfil de Acesso está Ativo">
                                            <span class="switch-label" data-on="SIM" data-off="NÃO"></span>
                                            <span class="switch-handle"></span>
                                        </label>
                                        <div id="error-active" class="error invalid-feedback" style="display: none;"></div>
                                    </div>

                                </form>
                            </fieldset>
                        
                        </div>  
                        <div class="tab-pane fade" id="autorizacoes" role="tabpanel" aria-labelledby="autorizacoes-tab" style="padding-top: 20px;">
                        
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Lista de Ações</legend>

                                <input type="hidden" id="perfil_id" value="">
                                <div class="table-responsive col-md-12">
                                    <table id="tblAuthorizations" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ação</th>
                                                <th>Rota</th>
                                                <th>Autorizada</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblBodyAuthorizations">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot></tfoot>                
                                    </table> 
                                </div>
                            </fieldset>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalAuthorizationsShow" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Autorizações do Usuário</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-sm-12">
                            
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Lista de Autorizações do Usuário</legend>
                                <div class="table-responsive col-md-12">
                                    <table id="tblActiveAuthorizations" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Sistema</th>
                                                <th>Entidade</th>
                                                <th>Ação</th>
                                                <th>Rota</th>
                                                <th>Autorizada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot></tfoot>                
                                    </table>
                                </div>
                            </fieldset>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

@stop

{{-- Add common Javascript/Jquery code --}}
@push('js')

    <!-- APP js of all pages -->

    <!-- DataTables JS -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>


    <!--  page behavior script -->
    <script type="text/javascript"> 

        $(document).ready(function () {

            $('#btnExcluirFoto').on('click', function() {
                // Limpa o input file
                $('#photo').val('');

                // Atualiza preview para avatar padrão
                $('#preview-image').attr('src', '/storage/users/avatar.jpg');
                
                // Marcar um flag hidden ou limpar o campo photo para informar backend
                $('#photo-removed').val('1'); // vamos criar esse input hidden

                // Opcional: esconder o botão "Remover foto" pois já está removido
                // $(this).hide();
                // $(this).prop('disabled', true);
            });            

            var id = '';
            // var action = '{{ url()->current() }}';
            var action = 'user';
            var authorizations = '';
            var btnInsert = '';
            var btnEdit = '';
            var btnDestroy = '';
            // console.log(action);

            const ERROR_HTTP_STATUS = new Set([401, 419]);

            /** 
             * manages X-CSRF-TOKEN and redirects to login if not authenticated
             */
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },  // validate the X-CSRF-TOKEN
                statusCode: { 401: function() { window.location.href = "/login";} },        // 401-UNAUTHORIZED redirects to login
            });

            /*
            * List the record data table
            */
            $('#datatables').DataTable({
                // serverSide: true,                                // this require instalation YajraDataTables
                processing: true,
                responsive: true,
                autoWidth: true,
                // order: [ 0, 'desc' ],
                lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "{{ __('acl.crud.todos')}}"]], 
                pageLength: 10,
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",        // $('#filterSelect1').val()
                    url: "{{ url()->current() }}",                  // current route
                    data: function(param) {                         // send dynamic parameter via GET to the Controller
                        param.filterSelect1 = $('#filterSelect1').val();                      // Adds the value of the #type field to the request parameters
                        param.filterSelect2 = $('#filterSelect2').val();                      // Adds the value of the #value field to the request parameters
                        param.filterSelect3 = $('#filterSelect3').val();                      // Adds the value of the #value field to the request parameters
                        param.filterSelect4 = $('#filterSelect4').val();                      // Adds the value of the #value field to the request parameters
                    },
                    // dataSrc: '',                                 // '' eliminates the need for data[] but needs to be in accordance with the Controller 
                    dataSrc: function (response) {
                        let authorizations = response.authorizations;   // Authorized routes
                        // console.log(authorizations);

                        // Insert New button control
                        if (response.authorizations.includes(action + '.store')) { $("#btnInsertNew").show(); } else { $("#btnInsertNew").hide(); }

                        // Modal edition save button control
                        if (response.authorizations.includes(action + '.update')) { $("#btnSave").show(); } else { $("#btnSave").hide(); }

                        return response.data;                           // Returns list of data to DataTables
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 401) { window.location.href = "{{ url('/login')}}";}     // 401-UNAUTHORIZED envia para login
                        if (xhr.status == 403) { window.location.href = "{{ url('/home') }}";}     // 403-FORBIDDEN envia para home
                    }
                },   
                rowId: 'id',    // set line id <tr id=""> as the id columns field 
                columns: [ 
                    {"data": "id", "name": "id", "class": "dt-right", "title": "#", "width": "20px"},
                    // {"data": null, "name": "", "class": "dt-left", "width": "100px", "title": "Org",
                    //     render: function (data) { return '<b>' + 'Org' + '</b>';}
                    // },
                    {"data": "name", "name": "name", "class": "dt-left", "width": "50px", "title": "{{ __('acl.user.columns.name-name') }}"},
                    {"data": "email", "name": "email", "class": "dt-left", "width": "180px", "title": "{{ __('acl.user.columns.email-name') }}",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    // Organizations
                    {"data": null, "name": "", "class": "dt-left", "width": "100px", "title": "Organizações",
                        render: function (data, type, row)  {

                            var btnAssignOrganization = '';
                            if (row.authorizations.includes(action + '.toggleOrganiz')) {
                                btnAssignOrganization = '<button class="btn btn-xs btn-success btnGrantOrganization" data-toggle="tooltip" title="Conceder Organizações ao Usuário"><i class="fas fa-fw fa-key"></i> Conceder</button> <br/>';
                            }

                            var lblOrganizations = '';
                            $.each(row.organizations, function(i, obj) {
                                lblOrganizations += '<span class="badge badge-info" data-toggle="tooltip" title="' + obj.name + '"><i class="fas fa-fw fa-building"></i> ' + obj.acronym + '</span><br/>';
                            })                        
                        
                            return btnAssignOrganization + lblOrganizations;
                        }
                    },
                    // Systems
                    {"data": null, "name": "", "class": "dt-left", "width": "100px", "title": "Sistemas",
                        render: function (data, type, row)  {
                            // return 'Systems';

                            var btnGrantSystem = '';
                            if (row.authorizations.includes(action + '.toggleSystem')) {
                                btnGrantSystem = '<button class="btn btn-xs btn-success btnGrantSystem" data-toggle="tooltip" title="Conceder Sistema ao Usuário"><i class="fas fa-fw fa-key"></i> Conceder</button> <br/>';
                            }

                            var lblSystems = '';
                            $.each(row.systems, function(i, obj) {
                                lblSystems += '<span class="badge badge-info" data-toggle="tooltip" title="' + obj.description	+ '"><i class="fas fa-fw fa-desktop"></i> ' + obj.acronym	 + '</span><br/>';
                            })                        
                        
                            return btnGrantSystem + lblSystems;
                        }
                    },
                    // Roles
                    {"data": null, "name": "", "class": "dt-left", "width": "100px", "title": "Perfis de Acesso",
                        render: function (data, type, row) { 

                            var btnGrantRole = '';
                            if (row.authorizations.includes(action + '.toggleRole')) {
                                btnGrantRole = '<button class="btn btn-xs btn-success btnGrantRole" data-toggle="tooltip" title="Conceder Perfis de Acesso ao Usuário"><i class="fas fa-fw fa-key"></i> Conceder</button> <br/>';
                            }
                        
                            var tblAutorizacoesLinhas = '';
                            $.each(row.profiles, function(i, obj){
                                tblAutorizacoesLinhas += '<span class="badge badge-info btnRoleShow" data-role-id="' + obj.id + '" data-toggle="tooltip" title="Manter as Autorizações do Perfil de Acesso: ' + obj.name + '"><i class="fas fa-fw fa-shield-alt"></i> ' + obj.name + '</span><br/>';
                            })                        
                        
                            return btnGrantRole + tblAutorizacoesLinhas;                        
                        }
                    },
                    // ACL do current User - Actives Authorizations's User List without repetition
                    {"data": null, "name": "", "class": "dt-left", "width": "40px", "title": "Autorizações",
                        render: function (data, type, row) { 

                            return btnGrantRole = '<button class="btn btn-xs btn-info btnShowUserAuthorizations" data-toggle="tooltip" title="Ver as Autorizações Ativas do Usuário"><i class="fas fa-fw fa-user-shield"></i> Ver</button> ';
                        }
                    },                    
                    {"data": "active", "name": "", "class": "dt-center", "width": "30px", "title": "{{ __('acl.user.columns.active-name') }}",
                        render: function (data) { return '<span class="' + ( data == 'Y' ? 'text-primary' : 'text-danger') + '">' + ( data == 'Y' ? '{{ __('acl.crud.columns_data.yes') }}' : '{{ __('acl.crud.columns_data.no') }}' ) + '</span>';}
                    },
                    // Buttons
                    {"data": null, "actions": "", "orderable": false, "class": "dt-center", "width": "120px", "title": "{{ __('acl.user.columns.actions-name') }}",
                        render: function (data, type, row) {  

                            btnEdit = '';                 
                            btnDestroy = '';                
                            // console.log(data); 
                            // console.log(row.authorizations); 

                            // button Show control
                            if (row.authorizations.includes(action + '.show')) {
                                btnEdit = '<button type="button" class="btnEdit btn btn-info btn-xs" data-operation="ver" data-toggle="tooltip" title="{{ __('acl.crud.btnShowTip') }}">{{ __('acl.crud.btnShow') }}</button> ';
                            }

                            // button Edit control
                            if (row.authorizations.includes(action + '.update')) {
                                btnEdit = '<button type="button" class="btnEdit btn btn-primary btn-xs" data-operation="save" data-toggle="tooltip" title="{{ __('acl.crud.btnEditTip') }}"><i class="fas fa-fw fa-edit"></i> {{ __('acl.crud.btnEdit') }}</button> ';
                            }

                            // button Destroy control
                            if (row.authorizations.includes(action + '.destroy')) {
                                btnDestroy = '<button type="button" class="btnDestroy btn btn-danger btn-xs" data-operation="excluir" data-toggle="tooltip" title="{{ __('acl.crud.btnDestroyTip') }}"><i class="fas fa-fw fa-trash"></i> {{ __('acl.crud.btnDestroy') }}</button> ';
                            }

                            return btnEdit + btnDestroy; 
                        }
                    },
                ],
            });

            $("#datatables").delegate('tr td .btnGrantRole', 'click', function (e) {
                e.stopImmediatePropagation();                

                id = $(this).parents('tr').attr("id");
                const userName = $(this).parents('tr').find('td:eq(1)').text();

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/listRoles") }}",
                    data: {"id": id},
                    dataType: 'json',
                    success: function (data) {

                        tblPerfis = '';
                        $habilitado = '';
                        // console.log(data);

                        $.each(data, function(i, obj){

                            tblPerfis += '' + 
                            '<tr id="' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td>' + obj.id + ' ' + obj.name + '</td>' + 
                                // '<td>' + obj.nome + '</td>' + 
                                '<td class="text-center">' + ( id >= 1 ? // Usuário 1-Admin sempre têm todos Perfis de Acesso
                                    '<label class="switch">' + 
                                    '<input type="checkbox" id="chk' + obj.id + '" ' + obj.granted + ' data-user_id="' + id + '" data-role_id="' + obj.id + '" class="switch-input">' + 
                                    '<span class="switch-label" data-on="SIM" data-off="NÃO"></span>' + 
                                    '<span class="switch-handle"></span>' + 
                                    '</label>' : 'SIM' ) +
                                '</td>' + 
                            '</tr>' + "\n";
                        })

                        tblPerfis = tblPerfis ? tblPerfis : '<tr><td class="text-center" colspan="3">Nenhuma registro</td></tr>';

                        $('#modalGrantRole #tblRolesGranted tbody').empty().append(tblPerfis);     // adiciona as linhas na tabela do modal
                        $('#modalGrantRole #user_id').val(id);                                    // carrega o User ID no modal
                        $('#modalGrantRole #modalLabel').html('<h5>Usuário: <span class="badge badge-primary">' + userName + '</span></h5>');
                        $('#modalGrantRole').modal('show');                        
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}";
                            return;
                        } 

                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');
                    }
                }); 
            });

            $("#datatables").delegate('tr td .btnGrantSystem', 'click', function (e) {
                e.stopImmediatePropagation();                

                id = $(this).parents('tr').attr("id");
                const user_nome = $(this).parents('tr').find('td:eq(1)').text();

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/listSystems") }}",
                    data: {"id": id},
                    dataType: 'json',
                    success: function (data) {

                        tblSystems = '';
                        $habilitado = '';
                        // console.log(data);

                        $.each(data, function(i, obj){

                            tblSystems += '' + 
                            '<tr id="' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td>' + obj.id + ' ' + obj.name + '</td>' + 
                                // '<td>' + obj.nome + '</td>' + 
                                '<td class="text-center">' + ( id >= 1 ? // Usuário 1-Admin sempre têm todos Perfis de Acesso
                                    '<label class="switch">' + 
                                    '<input type="checkbox" id="chk' + obj.id + '" ' + obj.granted + ' data-user_id="' + id + '" data-system_id="' + obj.id + '" class="switch-input">' + 
                                    '<span class="switch-label" data-on="SIM" data-off="NÃO"></span>' + 
                                    '<span class="switch-handle"></span>' + 
                                    '</label>' : 'SIM' ) +
                                '</td>' + 
                            '</tr>' + "\n";
                        })

                        tblSystems = tblSystems ? tblSystems : '<tr><td class="text-center" colspan="3">Nenhuma registro</td></tr>';
                        
                        $('#modalGrantSystem #tblSystemsGranted tbody').empty().append(tblSystems);     // adiciona as linhas na tabela do modal
                        $('#modalGrantSystem #user_id').val(id);                                        // carrega o User ID no modal
                        $('#modalGrantSystem #modalLabel').html('<h5>Usuário <span class="badge badge-primary">' + user_nome + '</span></h5>');
                        $('#modalGrantSystem').modal('show');                        
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}";
                            return;
                        } 

                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');
                    }
                }); 
            });
            
            $("#datatables").delegate('tr td .btnGrantOrganization', 'click', function (e) {
                e.stopImmediatePropagation();                

                id = $(this).parents('tr').attr("id");
                const user_nome = $(this).parents('tr').find('td:eq(1)').text();

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/listOrganiz") }}",
                    data: {"id": id},
                    dataType: 'json',
                    success: function (data) {

                        tblOrganizations = '';

                        $.each(data, function(i, obj){

                            tblOrganizations += '' + 
                            '<tr id="' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td>' + obj.id + ' ' + obj.name + '</td>' + 
                                '<td class="text-center">' + 
                                    '<div class="form-check">' + 
                                        '<input class="form-check-input organization-checkbox" type="checkbox" data-user_id="' + id + '" data-organization_id="' + obj.id + '" ' + obj.granted + ' id="chk' + obj.id + '" >' + 
                                    '</div>' + 
                                '</td>' + 
                            '</tr>' + "\n";
                        })

                        tblOrganizations = tblOrganizations ? tblOrganizations : '<tr><td class="text-center" colspan="2">Nenhuma registro</td></tr>';
                        
                        $('#modalGrantOrganization #tblOrganizationsGranted tbody').empty().append(tblOrganizations);       // adiciona as linhas na tabela do modal
                        $('#btnToggleOrganization').data('user_id',id);                                                      // carrega o User ID no btn do modal
                        $('#modalGrantOrganization #modalLabel').html('<h5>Usuário <span class="badge badge-primary">' + user_nome + '</span></h5>');
                        $('#modalGrantOrganization').modal('show');                        
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}";
                            return;
                        } 

                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');
                    }
                }); 
            });

            $("#datatables").delegate('tr td .btnRoleShow', 'click', function (e) {
                e.stopImmediatePropagation(); 

                // id = $(this).parents('tr').attr("id");
                // const user_nome = $(this).parents('tr').find('td:eq(2)').text();
                const roleId = $(this).data('role-id');
                const userName = $(this).parents('tr').find('td:eq(1)').text();

                /**
                 * NOTA AQUI SERIA MELHOR UM DATATABLES Ajax Reload como faço no Voluntary
                 * para repolar as autorizaçẽos - não está profissional
                 */

                // var tblAuthorizations = $('#tblAuthorizations').DataTable({
                // lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "Todos"]], 
                // pageLength: 5,
                // autoWidth: true,
                //     // columns: [ 
                //     //     {"data": "id", "name": "id", "class": "dt-right", "title": "#", "width": "20px"},
                //     //     {"data": "id", "name": "id", "class": "dt-right", "title": "#", "width": "20px"},
                //     //     {"data": "id", "name": "id", "class": "dt-right", "title": "#", "width": "20px"},
                //     // ]
                // });

                $.ajax({
                    type: "GET",
                    url: "profile/show",
                    data: { "id": roleId },
                    dataType: 'json',
                    success: function (response) {

                        // monta guia detalhes do Perfil
                        $('#modalRoleShow #modalLabel').html('Usuário <span class="badge badge-dark">' + userName + '</span><h5>Perfil de Acesso <span class="badge badge-primary">' + response.name + '</span></h5>');
                        $('#modalRoleShow #id').val(response.id);
                        $('#modalRoleShow #name').val(response.name);
                        $('#modalRoleShow #description').val(response.description);
                        $('#modalRoleShow #active').prop('checked', (response.active == "Y" ? true : false));
                        
                        // monta guia tabela de autorizações
                        tblAuthorizationsLines = '';
                        $.each(response.authorizations, function(i, obj){
                            tblAuthorizationsLines += '' + 
                            '<tr id="tr' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td>' + obj.action.action + '</td>' + 
                                '<td>' + obj.action.route  + '</td>' + 
                                '<td class="text-center">' + 
                                    '<label class="switch">' + 
                                    '<input type="checkbox" id="' + obj.id + '" data-authorization_id="' + obj.id + '" ' + ( obj.active == 'Y' ? 'checked' : '' ) + '  class="switch-input">' + 
                                    '<span class="switch-label" data-on="SIM" data-off="NÃO"></span>' + 
                                    '<span class="switch-handle"></span>' + 
                                    '</label>' + 
                                '</td>' + 
                            '</tr>';
                        })

                        // tblAuthorizationsLines = tblAuthorizationsLines ? tblAuthorizationsLines : '<tr><td class="text-center" colspan="4">Nenhuma Autorização encontrada</td></tr>';
                        tblAuthorizationsLines = tblAuthorizationsLines ? tblAuthorizationsLines : '<tr><td></td><td></td><td></td><td></td></tr>';

                        // se a datatables existe, destroy
                        if ($.fn.DataTable.isDataTable('#tblAuthorizations')) {
                            $('#tblAuthorizations').DataTable().destroy();
                        }

                        // adiciona as novas linhas na DataTables
                        $('#tblBodyAuthorizations').empty().append(tblAuthorizationsLines);    // adiciona as linhas na tabela   

                        // Renderiza a nova DataTable com os dados populados
                        $('#tblAuthorizations').DataTable({
                            lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "Todos"]], 
                            pageLength: 5,
                            autoWidth: true,
                        });
                        $('#modalRoleShow').modal('show');                             // show modal                          
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) { window.location.href = "{{ url('/login') }}"; return; } 
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');                        
                    }
                }); 
            });

            $("#datatables").delegate('tr td .btnShowUserAuthorizations', 'click', function (e) {
                e.stopImmediatePropagation(); 

                const userId = $(this).parents('tr').attr("id");
                const userName = $(this).parents('tr').find('td:eq(1)').text();

                $.ajax({
                    type: "GET",
                    url: "{{ url("user/listActiveAuth") }}",
                    data: { "id": userId },
                    dataType: 'json',
                    success: function (response) {

                        // monta tabela de autorizações do User
                        tbtUserAuthorizations = '';

                        $.each(response, function(i, obj) {

                            listSystems = '';
                            // $.each(obj.profile.systems, function(i, system) {
                            //     listSystems += system.acronym + ' ';
                            // });

                            tbtUserAuthorizations += '' + 
                            '<tr id="tr' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td class="' + ( obj.entity_id	<= 7 ? 'font-weight-lighter' : '' ) + '">' + obj.authorizations[0].profile.system.acronym + '</td>' + 
                                // '<td>' + listSystems	+ '</td>' + 
                                '<td class="' + ( obj.entity_id	<= 7 ? 'font-weight-lighter' : '' ) + '">' + obj.entity.model + '</td>' + 
                                '<td class="' + ( obj.entity_id	<= 7 ? 'font-weight-lighter' : 'font-weight-bold' ) + '">' + obj.action + '</td>' + 
                                '<td class="' + ( obj.entity_id	<= 7 ? 'font-weight-lighter' : '' ) + '">' + obj.route + '</td>' + 
                                // '<td class="text-center">' + 'SIM' + '</td>' + 
                                '<td class="text-center text-primary">' + ( obj.authorizations[0].active == 'Y' ? 'SIM' : 'NÃO' ) + '</td>' + 
                            '</tr>';
                        })
                        // console.log(response);
                        // <span class="' + ( data == 'Y' ? 'text-primary' 

                        tbtUserAuthorizations = tbtUserAuthorizations ? tbtUserAuthorizations : '<tr><td class="text-center" colspan="6">Usuário sem Autorizações Ativas</td></tr>';
                        $('#modalAuthorizationsShow #tblActiveAuthorizations tbody').empty().append(tbtUserAuthorizations);     // adiciona as linhas na tabela do modal
                        $('#modalAuthorizationsShow #modalLabel').html('Usuário <span class="badge badge-primary">' + userName + '</span></h5>');
                        $('#modalAuthorizationsShow').modal('show');                        
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) { window.location.href = "{{ url('/login') }}"; return; } 
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');                        
                    }
                }); 
            });

            /**
             * Grant or Revoke Systems to a User
             */
            $("#tblSystemsGranted").delegate('tr :checkbox', 'click', function (e) {
                e.stopImmediatePropagation();                 

                var chkObjeto   = $(this).attr("id");
                var chkUser     = $(this).data("user_id");
                var chkPerfil   = $(this).data("system_id");
                var chkCheked   = $(this).is(":checked") ? "Y" : "N";
                var operacao    = $(this).is(":checked") ? "assignSystem" : "revokeSystem";

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/toggleSystem") }}",
                    data: {"operacao": operacao, "user_id":chkUser, "system_id":chkPerfil },
                    dataType: 'json',
                    success: function (response) {
                        $('#btnRefresh').trigger('click');
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}";
                            return;
                        } 

                        $('#' + chkObjeto + ':checkbox').prop('checked', (chkCheked == 'Y' ? false : true));
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');                        
                    }
                });                 
            
            });

            /**
             * Grant or Revoke Roles to a User
             */
            $("#tblRolesGranted").delegate('tr :checkbox', 'click', function (e) {
                e.stopImmediatePropagation();                 

                var chkObjeto  = $(this).attr("id");
                var user_id    = $(this).data("user_id");
                var role_id    = $(this).data("role_id");
                var chkCheked  = $(this).is(":checked") ? "Y" : "N";
                var operation  = $(this).is(":checked") ? "assignRole" : "revokeRole";

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/toggleRole") }}",
                    data: {"operation": operation, "user_id": user_id, "role_id": role_id },
                    dataType: 'json',
                    success: function (response) {
                        $('#btnRefresh').trigger('click');
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}";
                            return;
                        } 

                        $('#' + chkObjeto + ':checkbox').prop('checked', (chkCheked == 'Y' ? false : true));                        
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');                        
                    }
                });                             
            });

            /**
             * Grant or Revoke Authorizations to a Action
             */
            $("#tblAuthorizations").delegate('tr :checkbox', 'click', function (e) {
                e.stopImmediatePropagation();                 

                var chkObjeto        = $(this).attr("id");
                var chkAuthorization = $(this).data("authorization_id");
                var chkCheked        = $(this).is(":checked") ? "Y" : "N";
                var chkOperation     = $(this).is(":checked") ? "assignAction" : "revokeAction";

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/toggleAuthorz") }}",
                    data: {"authorization_id": chkAuthorization, "operation": chkOperation },
                    dataType: 'json',
                    success: function (response) {
                        // $('#btnRefresh').trigger('click');
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}";
                            return;
                        } 

                        $('#' + chkObjeto + ':checkbox').prop('checked', (chkCheked == 'Y' ? false : true));                        
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');                        
                    }
                });                 
            });

            /**
             * Grant or Revoke Organizations to a User
             */
            $('#btnToggleOrganization').on("click", function (e) {
                e.stopImmediatePropagation();                

                let userId                = $(this).data("user_id");
                let selectedOrganizations = [];

                // Percorre os checkboxes marcados e adiciona ao array
                $(".organization-checkbox:checked").each(function() {
                    selectedOrganizations.push($(this).data('organization_id'));
                });                

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/toggleOrganiz") }}",
                    data: { "user_id":userId, organizations: selectedOrganizations },
                    dataType: 'json',
                    success: function (response) {
                        $('#modalGrantOrganization').modal('hide');
                        $('#btnRefresh').trigger('click');
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) { window.location.href = "{{ url('/login') }}"; return; } 

                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');                        
                    }
                });                 
            
            });

            /*
            * Send this code after ready to app.blade page
            * Extra buttoms actions
            */
            @foreach(optional($entityConfig)->pageButtons ?? [] as $button) 
                $('#{{ $button->btnName }}').on("click", function (e) {
                    e.stopImmediatePropagation();
                    
                    alert('{{ $button->btnName }}');
                    // ver voluntary v_EntidadeListarJSON.php
                    // window.open();
                });             
            @endforeach

            /**
             * Implementar um click para todos bottuns numa evento só
             * o button target pode ser: 
             *  '_blanc' - abre um window
             *  '_ajax' - dispara um Ajax
             *  '_modal' - abre um modal inrerido nesta view
             */

        });

    </script>    

@endpush