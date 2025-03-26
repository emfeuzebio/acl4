@extends('layouts.app')

@section('title', __('acl.entity.title'))

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
                            <label class="form-label">{{ __('acl.entity.columns.id-name') }}</label>
                            <input class="form-control" value="" type="text" id="id" name="id" placeholder="" readonly data-toggle="tooltip" title="{{ __('acl.entity.columns.id-tip') }}">
                        </div>                         

                        <!-- <div class="form-group">
                            <label class="form-label">{{ __('acl.entity.columns.system_id-name') }}</label>
                            <select name="system_id" id="system_id" class="form-control selectpicker" data-style="form-control" data-live-search="true" data-toggle="tooltip" data-placement="top" title="{{ __('acl.entity.columns.system_id-tip') }}">
                                <option value="">{{ __('acl.entity.columns.system_id-select') }}  </option>
                                @foreach( $systems as $system )
                                <option value="{{$system->id}}">{{$system->acronym}}</option>
                                @endforeach
                            </select>
                            <div id="error-system_id" class="error invalid-feedback" style="display: none;"></div>
                        </div>                            -->
                        
                        <div class="form-group">
                            <label class="form-label">{{ __('acl.entity.columns.model-name') }}</label>
                            <input class="form-control" value="" type="text" id="model" name="model" placeholder="{{ __('acl.entity.columns.model-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.entity.columns.model-tip') }}" >
                            <div id="error-model" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.entity.columns.table-name') }}</label>
                            <input class="form-control" value="" type="text" id="table" name="table" placeholder="{{ __('acl.entity.columns.table-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.entity.columns.table-tip') }}" >
                            <div id="error-table" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group input-group-sm">
                            <label class="form-label">{{ __('acl.entity.columns.description-name') }}</label>
                            <textarea class="form-control" id="description" name="description" placeholder="{{ __('acl.entity.columns.description-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.entity.columns.description-tip') }}" rows="4"></textarea>
                            <div id="error-description" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group input-group-sm">
                            <label class="form-label" data-toggle="tooltip" title="{{ __('acl.entity.columns.active-tip') }}">{{ __('acl.entity.columns.active-name') }}</label>
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

    <!-- Modal Editar Entidade e Permissões  -->
    <div class="modal fade" id="modalEditActions" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalEditActions').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="col-sm-12">

                        <fieldset class="border p-2">
                            <legend class="w-auto h5">Ações Inseridas</legend>

                            <div class="table-responsive col-md-12">
                                <table id="tblActions" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ação</th>
                                            <th>Rota</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tbody id="tblActionsBody">
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

                        <fieldset class="border p-2">
                            
                            <legend class="w-auto h5">Inserir Nova Ação</legend>
                            <form id="formAction" name="formAction" action="javascript:void(0)" class="form-horizontal" method="post">

                                <input class="form-control" value="" type="hidden" id="entity_id" name="entity_id">

                                <div class="form-group input-group-sm">
                                    <label class="form-label">Nome da Ação</label>
                                    <input class="form-control input-sm" value="" type="text" id="action" name="action" placeholder="Eg.: Print Global Report" data-toggle="tooltip" title="Informe o Nome da Ação" >
                                    <div id="error-action" class="error invalid-feedback" style="display: none;"></div>
                                </div>

                                <div class="form-group input-group-sm">
                                    <label class="form-label">Rota</label>
                                    <input class="form-control" value="" type="text" id="route" name="route" placeholder="Eg.: controller.update" data-toggle="tooltip" title="Informe a Rota da Ação" >
                                    <div id="error-route" class="error invalid-feedback" style="display: none;"></div>
                                </div>

                                <div class="form-group input-group-sm">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Eg. This Action allow print Global Report" data-toggle="tooltip" title="Informe a Descrição da Ação" rows="2"></textarea>
                                    <div id="error-description" class="error invalid-feedback" style="display: none;"></div>
                                </div>

                                <button id="btnInsertAction" class="btn btn-success btn-sm" data-toggle="tooltip" title="Adicionar uma nova Ação à Entidade Atual">Inserir</button>

                            </form>

                        </fieldset>                        

                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-8 text-left">
                        <label id="msgOperationAction" class="error invalid-feedback" style="display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-3 text-right">
                        <button type="button" class="btn btn-secondary btn-sm btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#modalEditActions').modal('hide');">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <ul>
    @foreach(auth()->user()->profiles as $profile)
        <li>{{ $profile->id }} {{ $profile->name }}</li>
    @endforeach
    </ul>    

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
            var entity = 'entity';
            var authorizations = '';
            var btnInsert = '';
            var btnEdit = '';
            var btnDestroy = '';
            var btnActions = '';
            var actionName = '';

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
            // $('#filterDiv1 #filterLabel1').html("{{ __('acl.entity.filterLabel1') }}"); // customise filter label
            // $('#filter_area').show();                                                   // show div filter area
            // $('#filterDiv1').show();                                                    // show div filter 1 
            
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
                autoWidth: true,
                // order: [ 0, 'desc' ],
                lengthMenu: [[10, 15, 30, 50, -1], [10, 15, 30, 50, "{{ __('acl.crud.todos')}}"]], 
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",
                    url: "{{ url()->current() }}",                  // current route
                    // data: { param1: 'x' },                       // send fixed parameter via GET to the Controller
                    data: function(param) {                         // send dynamic parameter via GET to the Controller
                        param.system_id = $('#filterSelect1').val();                      // Adds the value of the #type field to the request parameters
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
                    {"data": "id", "name": "entity.id", "class": "dt-right", "title": "#", "width": "20px"},
                    // {"data": "system.acronym", "name": "systems.acronym", "class": "dt-left", "width": "50px", "title": "{{ __('acl.entity.columns.system_id-name') }}"},
                    {"data": "model", "name": "entity.model", "class": "dt-left", "width": "200px", "title": "{{ __('acl.entity.columns.model-name') }}",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    {"data": "table", "name": "entity.table", "class": "dt-left", "width": "50px", "title": "{{ __('acl.entity.columns.table-name') }}"},
                    {"data": "description", "name": "entity.description", "class": "dt-left", "width": "30%", "title": "{{ __('acl.entity.columns.description-name') }}",},
                    {"data": "active", "name": "entity.active", "class": "dt-center", "width": "50px", "title": "{{ __('acl.entity.columns.active-name') }}",
                        render: function (data) { return '<span class="' + ( data == 'Y' ? 'text-primary' : 'text-danger') + '">' + ( data == 'Y' ? '{{ __('acl.crud.columns_data.yes') }}' : '{{ __('acl.crud.columns_data.no') }}' ) + '</span>';}
                    },
                    {"data": null, "actions": "", "orderable": false, "class": "dt-center", "width": "140px", "title": "{{ __('acl.entity.columns.actions-name') }}",
                        render: function (data, type, row) {  

                            btnEdit = '';                 
                            btnDestroy = '';               
                            btnActions = ''; 
                            // console.log(data); 
                            // console.log(row.authorizations); 

                            // button Show control
                            if (row.authorizations.includes(entity + '.show')) {
                                btnEdit = '<button type="button" class="btnEdit btn btn-primary btn-xs" data-operation="ver" data-toggle="tooltip" title="{{ __('acl.crud.btnShowTip') }}">{{ __('acl.crud.btnShow') }}</button> ';
                            }

                            // button Edit control
                            if (row.authorizations.includes(entity + '.update')) {
                                if (row.id <= 7) {
                                    btnEdit = '<button type="button" class="btnEdit btn btn-primary btn-xs" data-operation="ver" data-toggle="tooltip" title="{{ __('acl.crud.btnShowTip') }}">{{ __('acl.crud.btnShow') }}</button> ';
                                } else {
                                    btnEdit = '<button type="button" class="btnEdit btn btn-primary btn-xs" data-operation="save" data-toggle="tooltip" title="{{ __('acl.crud.btnEditTip') }}">{{ __('acl.crud.btnEdit') }}</button> ';
                                }
                            }

                            // button Destroy control
                            if (row.authorizations.includes(entity + '.destroy')) {
                                if (row.id > 7) {
                                    btnDestroy = '<button type="button" class="btnDestroy btn btn-danger btn-xs" data-operation="excluir" data-toggle="tooltip" title="{{ __('acl.crud.btnDestroyTip') }}">{{ __('acl.crud.btnDestroy') }}</button> ';
                                }
                            }

                            // button Actions control
                            if (row.authorizations.includes(entity + '.listActions')) {
                                btnActions = '<button class="btn btn-xs btn-info btnActions" data-toggle="tooltip" title="Gerenciar Ações da Entidade">Açẽos</button> ';
                            }

                            return btnEdit + btnDestroy + btnActions; 
                        }
                    },
                ],
            });

            /* 
            * Edit Entity's Actions 
            */
            $("#datatables").delegate('tr td .btnActions', 'click', function (e) {
                e.stopImmediatePropagation();            

                const entity_id = $(this).parents('tr').attr("id");
                const entity_name = $(this).parents('tr').find('td:eq(1)').text();

                $('#formAction').trigger("reset");
                $(".invalid-feedback").text('').hide();
                $('#modalEditActions #entity_id').val(entity_id);
                $('#modalEditActions #modalLabel').html('<h5>Entidade <span class="badge badge-primary">' + entity_name + '</span></h5>');
                $('#msgOperationAction').hide();

                // update interna modal Actions table and show modal
                $('#tblActionsBody').empty();
                ListEntityActions(entity_id);              
                $('#modalEditActions').modal('show');
            });   
            
            /*
            * Insert new Action on Entity
            */
            $('#btnInsertAction').on("click", function (e) {
                e.stopImmediatePropagation();

                $("#formAction .invalid-feedback").text('').hide();
                formData = new FormData($('#formAction').get(0));

                $.ajax({
                    type: "POST",
                    url: "{{ url("action/store") }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#msgOperationAction').removeClass().addClass('alert alert-success').html(response.message).show();
                        $('#formAction').trigger("reset");
                        ListEntityActions(response.sucesso);    // update interna modal Actions table
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) { window.location.href = "{{ url('/login') }}"; return; } 

                        // validator: vamos exibir todas as mensagens de erro do validador
                        // como o dataType não é JSON, precisa do responseJSON
                        $("#formAction .invalid-feedback").text('').hide();
                        $.each( error.responseJSON.errors, function( key, value ) {
                            $("#formAction #error-" + key ).text(value).show(); 
                        });

                        // exibe mensagem sobre sucesso da operação
                        if(error.responseJSON.message.indexOf("1062") != -1) {
                            $('#msgOperationAction').removeClass().addClass('error invalid-feedback').html("{{ __('acl.crud.errorMessage1062')}}").show();
                        } else {
                            $('#msgOperationAction').removeClass().addClass('error invalid-feedback').html(error.responseJSON.message).show();
                        }
                    }
                });                
            });       
            
            /*
            * Delete Route: btnDeleteRout . tblActionsBody
            */
            $("#tblActions").delegate('tr td .btnDeleteAction', 'click', function (e) {
                e.stopImmediatePropagation();   

                id = $(this).parents('tr').attr('id');
                actionName = $(this).parents('tr').find('td:eq(2)').text();
                // alert('btnDeleteRout: ' + id + ' actionName: ' + actionName);

                // //abre Form Modal Bootstrap e pede confirmação da Exclusão do Registro
                $("#confirmaExcluirModal .modal-body p").html('Você está certo que deseja Excluir a Ação: <b>' + actionName + '</b>?');
                $('#confirmaExcluirModal').modal('show');

                //se confirmar a Exclusão, exclui o Registro via Ajax
                $('#confirmaExcluirModal').find('.modal-footer #confirm').on('click', function (e) {
                    e.stopImmediatePropagation();

                    // alert('Excluir: ' + id);

                    $.ajax({
                        type: "POST",
                        url: "{{ url("action/destroy") }}",
                        data: {"id": id},
                        dataType: 'json',
                        success: function (response) {

                            $('#confirmaExcluirModal').modal('hide');
                            $('#msgOperationAction').removeClass().addClass('alert alert-success').html('Excluiu a Ação: <b>' + response.action + '</b> com sucesso.').show().delay(5000).fadeOut(1000);
                            $('#formAction').trigger("reset");
                            ListEntityActions(response.entity_id);
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

                
            });                  

            /*
            * Update interna modal Actions table
            */
            function ListEntityActions(entity_id) {

                $.ajax({
                    type: "GET",
                    url: "{{url("entity/listActions")}}",
                    data: {"id": entity_id},
                    dataType: 'json',
                    success: function (response) {

                        tblActions = '';
                        $.each(response.data, function(i, obj){
                            tblActions += '<tr id="' + obj.id + '"><td>' + (i+1) + '</td><td>' + obj.action + '</td><td>' + obj.route + '</td><td><button id="' + obj.id + '" ' + ( response.authorizations.ACLdestroy ? '' : 'disabled' ) + ' class="btnDeleteAction btn btn-danger btn-xs" data-toggle="tooltip" title="Excluir esta Ação">Excluir</button></td></tr>';
                        })
                        tblActions = tblActions ? tblActions : '<tr><td class="text-center" colspan="4">Nenhuma Ação Inserida</td></tr>';
                        $('#tblActionsBody').empty().append(tblActions);                // adiciona as linhas na tabela
                        $('#modalEditActions #btnInsertAction').prop('disabled', ( response.authorizations.ACLstore ? false : true ));

                    },
                    error: function (error) { 
                        if (ERROR_HTTP_STATUS.has(error.status)) { window.location.href = "{{ url('/login') }}"; return; } 
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');
                    }
                }); 
            }            

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