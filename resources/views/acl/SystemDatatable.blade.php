@extends('layouts.app')

@section('title', __(config('app.name')) . ' ' . __('acl.system.title'))

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
                            <label class="form-label">{{ __('acl.system.columns.id-name') }}</label>
                            <input class="form-control" value="" type="text" id="id" name="id" placeholder="" readonly data-toggle="tooltip" title="{{ __('acl.system.columns.id-tip') }}">
                        </div>                         

                        <!-- <div class="form-group">
                            <label class="form-label">{{ __('acl.system.columns.organization_id-name') }}</label>
                            <select name="organization_id" id="organization_id" class="form-control selectpicker" data-style="form-control" data-live-search="true" data-toggle="tooltip" data-placement="top" title="{{ __('acl.system.columns.name-tip') }}">
                                <option value="">{{ __('acl.system.columns.organization_id-select') }}  </option>
                                @foreach( $organizations as $organization )
                                <option value="{{$organization->id}}">{{$organization->acronym}}</option>
                                @endforeach
                            </select>
                            <div id="error-organization_id" class="error invalid-feedback" style="display: none;"></div>
                        </div>                            -->
                        
                        <div class="form-group">
                            <label class="form-label">{{ __('acl.system.columns.name-name') }}</label>
                            <input class="form-control" value="" type="text" id="name" name="name" placeholder="{{ __('acl.system.columns.name-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.system.columns.name-tip') }}" >
                            <div id="error-name" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.system.columns.acronym-name') }}</label>
                            <input class="form-control" value="" type="text" id="acronym" name="acronym" placeholder="{{ __('acl.system.columns.acronym-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.system.columns.acronym-tip') }}" >
                            <div id="error-acronym" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group input-group-sm">
                            <label class="form-label">{{ __('acl.system.columns.description-name') }}</label>
                            <textarea class="form-control" id="description" name="description" placeholder="{{ __('acl.system.columns.description-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.system.columns.description-tip') }}" rows="4"></textarea>
                            <div id="error-description" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group input-group-sm">
                            <label class="form-label" data-toggle="tooltip" title="{{ __('acl.system.columns.active-tip') }}">{{ __('acl.system.columns.active-name') }}</label>
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
            var entity = 'system';
            var authorizations = '';
            var btnInsert = '';
            var btnEdit = '';
            var btnDestroy = '';

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
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",
                    url: "{{ url()->current() }}",                  // current route
                    // data: { param1: 'x' },                       // send fixed parameter via GET to the Controller
                    data: function(param) {                         // send dynamic parameter via GET to the Controller
                        param.tipo = 'tipo';                        // Adds the value of the #type field to the request parameters
                        param.forma = 'forma';                      // Adds the value of the #value field to the request parameters
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
                    {"data": "id", "name": "system.id", "class": "dt-right", "title": "#", "width": "20px"},
                    // {"data": "organization.acronym", "name": "organization.organization_id", "class": "dt-left", "width": "50px", "title": "{{ __('acl.system.columns.organization_id-name') }}"},
                    {"data": "name", "name": "system.name", "class": "dt-left", "width": "200px", "title": "{{ __('acl.system.columns.name-name') }}",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    {"data": "acronym", "name": "system.acronym", "class": "dt-left", "width": "50px", "title": "{{ __('acl.system.columns.acronym-name') }}"},
                    {"data": "description", "name": "system.description", "class": "dt-left", "width": "30%", "title": "{{ __('acl.system.columns.description-name') }}",},
                    {"data": "active", "name": "system.active", "class": "dt-center", "width": "50px", "title": "{{ __('acl.system.columns.active-name') }}",
                        render: function (data) { return '<span class="' + ( data == 'Y' ? 'text-primary' : 'text-danger') + '">' + ( data == 'Y' ? '{{ __('acl.crud.columns_data.yes') }}' : '{{ __('acl.crud.columns_data.no') }}' ) + '</span>';}
                    },
                    {"data": null, "actions": "", "orderable": false, "class": "dt-center", "width": "100px", "title": "{{ __('acl.system.columns.actions-name') }}",
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