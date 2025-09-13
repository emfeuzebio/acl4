@extends('layouts.app')

@section('title', __(config('app.name')) . ' ' . __('acl.profile.title'))

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
                            <label class="form-label">{{ __('acl.profile.columns.id-name') }}</label>
                            <input class="form-control" value="" type="text" id="id" name="id" placeholder="" readonly data-toggle="tooltip" title="{{ __('acl.profile.columns.id-tip') }}">
                        </div>                         

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.entity.columns.system_id-name') }}</label>
                            <select name="system_id" id="system_id" class="form-control selectpicker" data-style="form-control" data-live-search="true" data-toggle="tooltip" data-placement="top" title="{{ __('acl.entity.columns.system_id-tip') }}">
                                <option value="">{{ __('acl.entity.columns.system_id-select') }}  </option>
                                @foreach( $systems as $system )
                                <option value="{{$system->id}}">{{$system->acronym}}</option>
                                @endforeach
                            </select>
                            <div id="error-system_id" class="error invalid-feedback" style="display: none;"></div>
                        </div>                           
                        
                        <div class="form-group">
                            <label class="form-label">{{ __('acl.profile.columns.name-name') }}</label>
                            <input class="form-control" value="" type="text" id="name" name="name" placeholder="{{ __('acl.profile.columns.name-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.profile.columns.name-tip') }}" >
                            <div id="error-name" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.profile.columns.acronym-name') }}</label>
                            <input class="form-control" value="" type="text" id="acronym" name="acronym" placeholder="{{ __('acl.profile.columns.acronym-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.profile.columns.acronym-tip') }}" >
                            <div id="error-acronym" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group input-group-sm">
                            <label class="form-label">{{ __('acl.profile.columns.description-name') }}</label>
                            <textarea class="form-control" id="description" name="description" placeholder="{{ __('acl.profile.columns.description-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.profile.columns.description-tip') }}" rows="4"></textarea>
                            <div id="error-description" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group input-group-sm">
                            <label class="form-label" data-toggle="tooltip" title="{{ __('acl.profile.columns.active-tip') }}">{{ __('acl.profile.columns.active-name') }}</label>
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

    <!-- Modal Grant System to Profile -->
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
                                <legend class="w-auto h5">Sistemas</legend>

                                <div class="table-responsive col-md-12">
                                    <table id="tblOrganizationsGranted" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Sistema</th>
                                                <th>Concedido ao Perfil</th>
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
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantSystem').modal('hide');">Fechar</button>
                        <button type="button" class="btn btn-primary btnToggleSystem" id="btnToggleSystem" data-profile_id="" data-toggle="tooltip" title="{{ __('acl.crud.btnSaveTip') }}">{{ __('acl.crud.btnSave') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editRoleAuthorizationsModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
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
                                        <label class="form-label">Nome</label>
                                        <input class="form-control" disabled value="" type="text" id="nome" name="nome" placeholder="" data-toggle="tooltip" title="Informe o Nome do Perfil de Acesso" >
                                        <div id="error-nome" class="error invalid-feedback" style="display: none;"></div>
                                    </div>

                                    <div class="form-group input-group-sm">
                                        <label class="form-label">Descrição</label>
                                        <textarea class="form-control" disabled id="descricao" name="descricao" placeholder="" data-toggle="tooltip" title="Informe a Descrição do Perfil de Acesso" rows="4"></textarea>
                                        <div id="error-descricao" class="error invalid-feedback" style="display: none;"></div>
                                    </div>

                                    <div class="form-group input-group-sm">
                                        <label class="form-label" data-toggle="tooltip" title="Marcar se o Perfil de Acesso está Ativo">Ativo</label>
                                        <label class="switch">
                                            <input type="checkbox" disabled id="ativo" name="ativo" class="switch-input" data-toggle="tooltip" title="Marcar se  o Perfil de Acesso está Ativo">
                                            <span class="switch-label" data-on="SIM" data-off="NÃO"></span>
                                            <span class="switch-handle"></span>
                                        </label>
                                        <div id="error-ativo" class="error invalid-feedback" style="display: none;"></div>
                                    </div>

                                </form>
                            </fieldset>
                        
                        </div>  
                        <div class="tab-pane fade" id="autorizacoes" role="tabpanel" aria-labelledby="autorizacoes-tab" style="padding-top: 20px;">
                        
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Ações Autorizadas</legend>

                                <input type="hidden" id="perfil_id" value="">
                                <div class="table-responsive col-md-12">
                                    <table id="tblAutorizacoes" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ação</th>
                                                <th>Rota</th>
                                                <th>Autorizada</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyAutorizacoes">
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

    <!-- Modal Grant System to User -->
    <div class="modal fade" id="modalGrantEntity" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantEntity').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Entidades</legend>
                                <input type="hidden" id="role_id" value="">

                                <div class="table-responsive col-md-12">
                                    <table id="tblEntitiesGranted" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Entidade</th>
                                                <th>Concedidos ao Perfil</th>
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
                        <label id="msgOperacaoEntity" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalGrantEntity').modal('hide');">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Entidade e Permissões  -->
    <div class="modal fade" id="modalAutorizacoesEditar" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalAutorizacoesEditar').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-sm-12">
                            
                            <fieldset class="border p-2">
                                <legend class="w-auto h5">Ações da Entidade</legend>
                                <div class="table-responsive col-md-12">
                                    <table id="tbtAuthorizations" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ação</th>
                                                <th>Rota</th>
                                                <th>Autorizada</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbtAuthorizationsLines">
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
                    <div class="col-md-5 text-left">
                        <label id="msgOperacaoPerfis" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalAutorizacoesEditar').modal('hide');">Fechar</button>
                        <!-- <button type="button" class="btn btn-primary" id="btnSalvar" data-toggle="tooltip" title="Salvar o registro (Alt+S)">Salvar</button> -->
                    </div>
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

            var id = '';
            var entity = 'profile';
            var action = 'profile';
            var authorizations = '';
            var btnInsert = '';
            var btnEdit = '';
            var btnDestroy = '';

            const ERROR_HTTP_STATUS = new Set([401, 419]);

            /** 
             * manages X-CSRF-TOKEN and redirects to login if not authenticated
             */
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },  // validate the X-CSRF-TOKEN
                statusCode: { 401: function() { window.location.href = "/login";} },        // 401-UNAUTHORIZED redirects to login
            });

            /** 
             * option active page filters  
             *      filter need customise filter field name GET in datatables above and after in ontroller->index() 
             *  data: function(param) {                         
             *            param.entity_id
             */
            // active page filter 1
            $('#filterDiv1 #filterLabel1').html("{{ __('acl.entity.filterLabel1') }}"); // customise filter label
            $('#filter_area').show();                                                   // show div filter area
            $('#filterDiv1').show();                                                    // show div filter 1 
            // $('#filterDiv2').show();                                                 // show div filter 1 
            // $('#filterDiv3').show();                                                 // show div filter 1 
            // $('#filterDiv4').show();                                                 // show div filter 1 
            // active page filter 2 ....            

            /*
            * List the record data table
            */
            $('#datatables').DataTable({
                // serverSide: true,                                // this require instalation YajraDataTables
                processing: true,
                responsive: true,
                // autoWidth: true,
                // order: [ 0, 'desc' ],
                lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "{{ __('acl.crud.todos')}}"]], 
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",
                    url: "{{ url()->current() }}",                  // current route
                    // data: { param1: 'x' },                       // send fixed parameter via GET to the Controller
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
                        if (response.authorizations.includes(entity + '.store')) { $("#btnInsertNew").show(); } else { $("#btnInsertNew").hide(); }

                        // Modal edition save button control
                        if (response.authorizations.includes(entity + '.update')) { $("#btnSave").show(); } else { $("#btnSave").hide(); }

                        return response.data;                           // Returns list of data to DataTables
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 401) { window.location.href = "{{ url('/login')}}";}     // 401-UNAUTHORIZED envia para login
                        if (xhr.status == 403) { window.location.href = "{{ url('/home') }}";}     // 403-FORBIDDEN envia para home
                    }
                },   
                rowId: 'id',    // set line id <tr id=""> as the id columns field 
                columns: [ 
                    {"data": "id", "name": "profile.id", "class": "dt-right", "title": "#", "width": "10px"},
                    {"data": "system.acronym", "name": "systems.acronym", "class": "dt-left", "width": "40px", "title": "{{ __('acl.entity.columns.system_id-name') }}"},
                    {"data": "name", "name": "profile.name", "class": "dt-left", "width": "80px", "title": "{{ __('acl.profile.columns.name-name') }}",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    {"data": "acronym", "name": "profile.acronym", "class": "dt-left", "width": "40px", "title": "{{ __('acl.profile.columns.acronym-name') }}"},
                    {"data": "description", "name": "profile.description", "class": "dt-left", "width": "25%", "title": "{{ __('acl.profile.columns.description-name') }}",},
                    // System 
                    // {"data": null, "name": "", "class": "dt-left", "width": "150px", "title": "Sistemas",
                    //     render: function (data, type, row)  {
                            
                    //         // var btnGrantSystem = '';
                    //         // if (row.authorizations.includes(action + '.toggleSystem')) {
                    //         //     btnGrantSystem = '<button class="btn btn-xs btn-success btnGrantSystem" data-toggle="tooltip" title="Conceder Sistema ao Usuário">Conceder</button> ';
                    //         // }

                    //         // var lblSystems = '';
                    //         // $.each(row.systems, function(i, obj) {
                    //         //     lblSystems += '<span class="badge badge-info" data-toggle="tooltip" title="' + obj.description	+ '">' + obj.acronym	 + '</span><br/>';
                    //         // })                        
                        
                    //         // return btnGrantSystem + lblSystems;                            

                    //         var btnGrantSystem = '';
                    //         if (row.authorizations.includes('user.toggleSystem')) {
                    //             btnGrantSystem = '<button class="btn btn-xs btn-success btnGrantSystem" data-toggle="tooltip" title="Conceder/Revogar Sistemas ao Perfil de Acesso (Role)">Conceder</button> ';
                    //         }

                    //         var lblSystems = '';
                    //         $.each(row.systems, function(i, obj) {
                    //             lblSystems += '<span class="badge badge-info" data-toggle="tooltip" title="Sistema: ' + obj.name + '">' + obj.acronym + '</span><br/>';
                    //         })                        
                        
                    //         return btnGrantSystem + lblSystems;                            
                    //     }                
                    // },
                    // Entities and Authorizations
                    {"data": null, "name": "", "class": "dt-left", "width": "150px", "title": "{{ __('acl.profile.columns.entityAuthrizations-name') }}",              
                        render: function (data, type, row)  {
                            
                            var btnGrantEntity = '';
                            if (row.authorizations.includes('profile.toggleEntity')) {
                                btnGrantEntity = '<button class="btn btn-xs btn-success btnGrantEntity" data-toggle="tooltip" title="Conceder/Revogar Entidade (Model) ao Perfil de Acesso (Role)">Conceder</button> ';
                            }

                            var entidadePadrao = 7;
                            return btnGrantEntity + $.map(data.entities, function(item, i) {
                                return '<button id="' + item.id + '" class="btn btn-xs btn-' + ( item.id <= entidadePadrao ? 'default' : 'info btnGrantAction' ) + '" data-toggle="tooltip" title="' + ( item.id <= entidadePadrao ? 'Somente Administrador pode Autorizar Ações de Entidade Base.' : 'Autorizar Ações da Entidade' ) + ' (' + item.model + ')">' + item.model + '</button> ';
                            }).join(' '); 
                        }                
                    },
                    // Active
                    {"data": "active", "name": "profile.active", "class": "dt-center", "width": "20px", "title": "{{ __('acl.profile.columns.active-name') }}",
                        render: function (data) { return '<span class="' + ( data == 'Y' ? 'text-primary' : 'text-danger') + '">' + ( data == 'Y' ? '{{ __('acl.crud.columns_data.yes') }}' : '{{ __('acl.crud.columns_data.no') }}' ) + '</span>';}
                    },
                    // Buttons
                    {"data": null, "actions": "", "orderable": false, "class": "dt-center", "width": "70px", "title": "{{ __('acl.profile.columns.actions-name') }}",
                        render: function (data, type, row) {  

                            btnEdit = '';                 
                            btnDestroy = '';                
                            // console.log(data); 
                            // console.log(row.authorizations); 

                            // button Show control
                            if (row.authorizations.includes(entity + '.show')) {
                                btnEdit = '<button type="button" class="btnEdit btn btn-info btn-xs" data-operation="ver" data-toggle="tooltip" title="{{ __('acl.crud.btnShowTip') }}">{{ __('acl.crud.btnShow') }}</button> ';
                            }

                            // button Edit control
                            if (row.authorizations.includes(entity + '.update')) {
                                btnEdit = '<button type="button" class="btnEdit btn btn-primary btn-xs" data-operation="save" data-toggle="tooltip" title="{{ __('acl.crud.btnEditTip') }}">{{ __('acl.crud.btnEdit') }}</button> ';
                            }

                            // button Destroy control
                            if (row.authorizations.includes(entity + '.destroy')) {
                                btnDestroy = '<button type="button" class="btnDestroy btn btn-danger btn-xs" data-operation="excluir" data-toggle="tooltip" title="{{ __('acl.crud.btnDestroyTip') }}">{{ __('acl.crud.btnDestroy') }}</button> ';
                            }

                            return btnEdit + btnDestroy; 
                        }
                    },
                ],
            });

            /*
            * Grant or Revoke an Entity to a Profile (Role)
            */
            $("#datatables").delegate('tr td .btnGrantEntity', 'click', function (e) {
                e.stopImmediatePropagation();                

                profile_id = $(this).parents('tr').attr("id");
                system_id = $('#filterSelect1').val();

                // if (!system_id) {
                //     alert('NECESSÁRIO filtrar por um Sistema');
                //     return;
                // }

                const user_nome = $(this).parents('tr').find('td:eq(2)').text();

                $.ajax({
                    type: "POST",
                    url: "{{ url("entity/listEntities") }}",
                    data: {"profile_id": profile_id },
                    // data: {"profile_id": profile_id, "system_id": system_id },
                    dataType: 'json',
                    success: function (response) {

                        tblEntities = '';
                        $habilitado = '';

                        $.each(response, function(i, obj){

                            tblEntities += '' + 
                            '<tr id="' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td>' + obj.id + ' ' + obj.model + '</td>' + 
                                '<td class="text-center">' + ( obj.id > 7 ? // Usuário 1-Admin sempre têm todos Perfis de Acesso
                                    '<label class="switch">' + 
                                    '<input type="checkbox" id="chk' + obj.id + '" ' + ( obj.granted == 'Y' ? 'checked' : '' ) + ' data-role_id="' + profile_id + '" data-entity_id="' + obj.id + '" class="switch-input" data-toggle="tooltip" title="Grant/Revoke this Entity to Profile">' + 
                                    '<span class="switch-label" data-on="SIM" data-off="NÃO"></span>' + 
                                    '<span class="switch-handle"></span>' + 
                                    '</label>' : 'SIM' ) +
                                '</td>' + 
                            '</tr>' + "\n";
                        })

                        tblEntities = tblEntities ? tblEntities : '<tr><td class="text-center" colspan="2">Nenhuma registro</td></tr>';
                        
                        $('#modalGrantEntity #tblEntitiesGranted tbody').empty().append(tblEntities);     // adiciona as linhas na tabela do modal
                        $('#modalGrantEntity #role_id').val(profile_id);                                  // carrega o profile_id
                        $('#modalGrantEntity #modalLabel').html('<h5>Perfil de Acesso <span class="badge badge-primary">' + user_nome + '</span></h5>');
                        $('#modalGrantEntity').modal('show');                        
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

            /*
            * Grant or Revoke Systems to a Profile (Role)
            */
            $("#datatables").delegate('tr td .btnGrantSystem', 'click', function (e) {
                e.stopImmediatePropagation();                

                profileId = $(this).parents('tr').attr("id");
                const roleName = $(this).parents('tr').find('td:eq(1)').text();

                $.ajax({
                    type: "POST",
                    url: "{{ url("profile/listSystems") }}",
                    // url: "{{ url("user/listSystems") }}",
                    data: { "id": profileId },
                    dataType: 'json',
                    success: function (data) {

                        btnSystems = '';

                        $.each(data, function(i, obj){

                            btnSystems += '' + 
                            '<tr id="' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td>' + obj.id + ' ' + obj.name + '</td>' + 
                                '<td class="text-center">' + 
                                    '<div class="form-check">' + 
                                        '<input class="form-check-input system-checkbox" type="checkbox" data-profile_id="' + profileId + '" data-system_id="' + obj.id + '" ' + obj.granted + ' id="chk' + obj.id + '" >' + 
                                    '</div>' + 
                                '</td>' + 
                            '</tr>' + "\n";
                        })

                        btnSystems = btnSystems ? btnSystems : '<tr><td class="text-center" colspan="2">Nenhuma registro</td></tr>';
                        
                        $('#modalGrantSystem #tblOrganizationsGranted tbody').empty().append(btnSystems);                 // adiciona as linhas na tabela do modal
                        $('#btnToggleSystem').data('profile_id',profileId);                                                         // carrega o User ID no btn do modal
                        $('#modalGrantSystem #modalLabel').html('<h5>Perfil de Acesso <span class="badge badge-primary">' + roleName + '</span></h5>');
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

            /*
            * Grant or Revoke Action to a Entity
            */
            $("#datatables tbody").delegate('tr td .btnGrantAction', 'click', function (e) {
                e.stopImmediatePropagation();            

                const id = $(this).attr('id');
                const role_id = $(this).parents('tr').attr("id");
                const system_id = $('#filterSelect1').val();

                $.ajax({
                    type: "GET",
                    url: "{{ url("entity/listAuthorzs") }}",
                    data: { "entity_id": id, "role_id": role_id, "system_id": system_id },
                    dataType: 'json',
                    success: function (data) {

                        // console.log(data[0].action.entity.description);
                        $('#modalAutorizacoesEditar #modalLabel').html('Perfil de Acesso <span class="badge badge-dark">' + data[0].profile.name+ '</span><h5>Autorizar Ações na Entidade <span class="badge badge-primary">' + data[0].action.entity.model + '</span></h5>');
                        $('#modalAutorizacoesEditar').modal('show');

                        // monta tabela de autorizações
                        tbtAuthorizationsLines = '';
                        $.each(data, function(i, obj){
                            tbtAuthorizationsLines += '' + 
                            '<tr id="tr' + obj.id + '">' + 
                                '<td>' + (i+1) + '</td>' + 
                                '<td>' + obj.action.action + '</td>' + 
                                '<td>' + obj.action.route + '</td>' + 
                                '<td class="text-center">' + 
                                    '<label class="switch">' + 
                                    '<input type="checkbox" id="' + obj.id + '" ' + ( obj.active == 'Y' ? 'checked' : '' ) + ' class="switch-input" data-toggle="tooltip" title="Conceder/Revogar">' + 
                                    '<span class="switch-label" data-on="SIM" data-off="NÃO"></span>' + 
                                    '<span class="switch-handle"></span>' + 
                                    '</label>' + 
                                '</td>' + 
                            '</tr>';
                        })
                        tbtAuthorizationsLines = tbtAuthorizationsLines ? tbtAuthorizationsLines : '<tr><td class="text-center" colspan="4">Nenhuma Permissão concedida</td></tr>';
                        $('#tbtAuthorizationsLines').empty().append(tbtAuthorizationsLines);  //adiciona as linhas na tabela
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) { 
                            window.location.href = "{{ url('/login') }}"; return;
                        } 

                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');
                    }
                });                 
            });             

            /**
             * Grant or Revoke Authorization to a Profile for an Action
             */
            $("#tblEntitiesGranted").delegate('tr :checkbox', 'click', function (e) {
                e.stopImmediatePropagation();                 

                var chkObjeto   = $(this).attr("id");
                var chkCheked   = $(this).is(":checked") ? "Y" : "N";
                var role_id     = $(this).data("role_id");
                var entity_id   = $(this).data("entity_id");
                var operation   = $(this).is(":checked") ? "assignEntity" : "revokeEntity";

                $.ajax({
                    type: "POST",
                    url: "{{ url("profile/toggleEntity") }}",
                    data: {"role_id": role_id, "entity_id": entity_id, "operation": operation },
                    dataType: 'json',
                    success: function (response) {
                        $('#btnRefresh').trigger('click');
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}"; return;
                        } 

                        $('#' + chkObjeto + ':checkbox').prop('checked', (chkCheked == 'Y' ? false : true));
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');                        
                    }
                });                 
            });
            
            /**
             * Grant or Revoke Action Authorization to a Entity
             */
            $("#tbtAuthorizationsLines").delegate('tr :checkbox', 'click', function (e) {
                e.stopImmediatePropagation();                 

                var chkObjeto   = $(this).attr("id");
                var chkCheked   = $(this).is(":checked") ? "Y" : "N";
                var operation   = $(this).is(":checked") ? "assignAction" : "revokeAction";

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/toggleAuthorz") }}",
                    data: { "authorization_id": chkObjeto, "operation": operation },
                    dataType: 'json',
                    success: function (response) {
                        // $('#btnRefresh').trigger('click');
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) {
                            window.location.href = "{{ url('/login') }}"; return;
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
            $('#btnToggleSystem').on("click", function (e) {
                e.stopImmediatePropagation();                

                let profileId       = $(this).data("profile_id");
                let selectedSystems = [];

                // Percorre os checkboxes marcados e adiciona ao array
                $(".system-checkbox:checked").each(function() {
                    selectedSystems.push($(this).data('system_id'));
                });                

                $.ajax({
                    type: "POST",
                    url: "{{ url("profile/toggleSystem") }}",
                    data: { "profile_id":profileId, systems: selectedSystems },
                    dataType: 'json',
                    success: function (response) {
                        $('#modalGrantSystem').modal('hide');
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