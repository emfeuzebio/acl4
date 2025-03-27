@extends('adminlte::page')

{{-- Extend and customize the browser title --}}
@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop 

{{-- Extend and customize the page content header --}}
@section('content_header')
    <div class="row mb-2">
        <div class="m-0 text-dark col-sm-6">
            <h1 class="m-0 text-dark">@yield('page_title') @yield('page_subtitle')</h1>
            <!-- <div class="text-left h5"><b> Cadastro da Organização Militar</b></div> -->
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/home">@yield('breadcrumb1')</a></li>
                <li class="breadcrumb-item ">@yield('breadcrumb2')</li>
                <li class="breadcrumb-item active">@yield('breadcrumb3')</li>
            </ol>
        </div>
    </div>    
@stop

@section('content')

    <!-- Data of DataTables -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3 text-left h5"><b>@yield('table_title')</b></div>
                            
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
                        <div class="table-responsive col-md-12">
                            <table id="datatables" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                                <thead></thead>
                                <tbody></tbody>
                                <tfoot></tfoot>                
                            </table>
                            <br/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('content_body')

    <!-- modal excluir registro -->
    <div class="modal fade" id="confirmaExcluirModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Excluir Registro</h4>
                    <button type="button" class="close btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#msgOperacaoExcluir').text('');$('#confirmaExcluirModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">
                    <p></p>
                    <label id="msgOperacaoExcluir" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#msgOperacaoExcluir').text('');$('#confirmaExcluirModal').modal('hide');"><i class="fas fa-fw fa-times"></i>Cancelar</button>
                    <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Confirmar a Exclusão" id="confirm"><i class="fas fa-fw fa-trash"></i>Excluir</button>
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
    
    <!-- Edit User Profile Modal -->
    <div class="modal fade" id="editUserProfileModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Editar seu Perfil de Usuário</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="{{ __('acl.crud.btnCancelTip') }}" onClick="$('#editUserProfileModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <form id="formUserProfile" name="formUserProfile"  action="javascript:void(0)" class="form-horizontal" method="post">

                        <div class="form-group" id="form-group-id">
                            <label class="form-label">{{ __('acl.userprofile.columns.id-name') }}</label>
                            <input class="form-control" value="" type="text" id="id" name="id" placeholder="" readonly data-toggle="tooltip" title="{{ __('acl.userprofile.columns.id-tip') }}">
                        </div>                         
                        
                        <div class="form-group">
                            <label class="form-label">{{ __('acl.userprofile.columns.name-name') }}</label>
                            <input class="form-control" value="" type="text" id="name" name="name" placeholder="{{ __('acl.userprofile.columns.name-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.userprofile.columns.name-tip') }}" >
                            <div id="error-name" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('acl.userprofile.columns.email-name') }}</label>
                            <input class="form-control" value="" type="text" id="email" disabled placeholder="{{ __('acl.userprofile.columns.email-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.userprofile.columns.email-tip') }}" >
                            <div id="error-acronym" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group input-group-sm">
                            <label class="form-label">{{ __('acl.userprofile.columns.description-name') }}</label>
                            <textarea class="form-control" id="description" name="description" placeholder="{{ __('acl.userprofile.columns.description-placeholder') }}" data-toggle="tooltip" title="{{ __('acl.userprofile.columns.description-tip') }}" rows="4"></textarea>
                            <div id="error-description" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                    </form>        

                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <label id="msgOperacao" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-toggle="tooltip" title="{{ __('acl.crud.btnCancelTip') }}" onClick="$('#editUserProfileModal').modal('hide');">{{ __('acl.crud.btnCancel') }}</button>
                        <button type="button" class="btn btn-primary" id="btnSaveUserProfile" data-operation="save" data-toggle="tooltip" title="{{ __('acl.crud.btnSaveTip') }}">{{ __('acl.crud.btnSave') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

{{-- Add common Javascript/Jquery code --}}
@push('js')

    <!-- APP js todas as páginas -->

    <!-- DataTables JS -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- APP js todas as páginas - EUZ Customise -->
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
            $('.invalid-feedback').text('').hide();     // clean fields errors messages
            $('#formEntity').trigger("reset");          // clean data fields from form
            // alert('Fechou Modal');
        });  


        // DataTables operações padrão
        $(document).ready(function() {

            const ERROR_HTTP_STATUS = new Set([401, 419]); // 401-UNAUTHORIZED, 403-FORBIDDEN, 419-PAGE_EXPIRED, 404-NOT_FOUND, 500-INTERNAL_SERVER_ERROR
            let trId = 0;
            var operation = '';
            var entityName = @json(__('acl.' . request()->path() . '.entityName'));

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                statusCode: { 401: function() { window.location.href = "/login"; } }
            });    

            /**
             * Edit User Profile
             */
            $('#btnEditUserProfile').on("click", function (e) {
                e.stopImmediatePropagation();

                $.ajax({
                    type: "GET",
                    url: "{{ url("user/show") }}",
                    data: { "id": "{{ auth()->id() }}" },
                    dataType: 'json',
                    success: function (response) {

                        // loda User data on form fields
                        $.each(response, function( key, value ) {
                            if (key == 'active') {
                                $('#editUserProfileModal #formUserProfile #active').prop('checked', (response.active == "Y" ? true : false));
                            } else {
                                $('#editUserProfileModal #formUserProfile #' + key).val(value);
                            }
                        });                        

                        $('#editUserProfileModal').modal('show');
                    },
                    error: function (error) {
                        if (ERROR_HTTP_STATUS.has(error.status)) { window.location.href = "{{ url('/login') }}"; return; } 
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                    }    
                });                 
            });  
            
            /*
            * Save User Profile
            */
            $('#btnSaveUserProfile').on("click", function (e) {
                e.stopImmediatePropagation();

                // lets join form fields and use index form .get(0)
                const formData = new FormData($('#editUserProfileModal #formUserProfile').get(0));

                $.ajax({
                    type: "POST",
                    url: "{{ url("user/updateProfile") }}",
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        $('#alert .alert-content').html("{{ __('acl.crud.confirmMessageSave') }}<b> " + response.id + '</b>');
                        $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);
                        $('#editUserProfileModal').modal('hide');
                        location.reload();  // page refresh
                    },
                    error: function (error) {

                        // show errors fields messages from the validator
                        $("#editUserProfileModal .invalid-feedback").text('').hide();
                        $.each( error.responseJSON.errors, function( key, value ) {
                            $("#editUserProfileModal #error-" + key ).text(value).show(); 
                        });

                        // show error messages
                        $('#editUserProfileModal #msgOperacao').html(error.responseJSON.message).show();
                    }
                });                
            });            

            /*
            * Refresh button action
            */
            $('#btnRefresh').on("click", function (e) {
                e.stopImmediatePropagation();

                $('#datatables').DataTable().ajax.reload(null, false);
                $('#alert').trigger('reset').hide();
            });

            /*
            * Filter select change action
            */
            $('#filterSelect1, #filterSelect2, #filterSelect3, #filterSelect4').on("change", function (e) {
                e.stopImmediatePropagation();

                $('#datatables').DataTable().ajax.reload(null, false);
            });            

            /*
            * New Record button action
            */
            $('#btnInsertNew').on("click", function (e) {
                e.stopImmediatePropagation();

                $('#editModal #form-group-id').hide();                                  // hide register ID
                // $('#formEntity').trigger("reset");                                   // limpa mensagens de erro
                // $(".invalid-feedback").text('').hide();                              // hide all error displayed
                $('#editModal #modalLabel').html("{{ __('acl.crud.insertRegLabel')}} " + entityName);    // modal title
                $("#editModal #btnSave").hide();                                        // hide btnSave
                $("#editModal #btnInsert").show();                                     // show btnInsert
                $('#editModal #formEntity #active').prop('checked', true);              // default Y-True
                $('#editModal').modal('show');                                          // show modal 
            });     
            
            /*
            * Edit button action
            */
            $("#datatables tbody").delegate('tr td .btnEdit', 'click', function (e) {
                e.stopImmediatePropagation();            

                const id = $(this).parents('tr').attr("id");

                $.ajax({
                    type: "GET",
                    url: "{{ url()->current() }}/show",
                    data: { "id": id },
                    dataType: 'json',
                    success: function (response) {

                        $('#editModal #modalLabel').html("{{ __('acl.crud.editRegLabel')}} " + entityName);
                        $('#editModal #form-group-id').show();                  // sendo uma edição mostra o ID do registro
                        $("#editModal #btnInsert").hide();                      // esconde o btn Inserir  
                        $('#editModal').modal('show');                          // mostra o modal de edição de dados

                            // $('#formEntity #id').val(response.id);
                            // $('#formEntity #sigla').val(response.sigla);
                            // $('#formEntity #nome').val(response.nome);
                            // $('#formEntity #descricao').val(response.descricao);
                            // $('#formEntity #active').prop('checked', (response.ativo == "SIM" ? true : false));

                        // carregas os dados dos campos no Form
                        $.each(response, function( key, value ) {
                            if (key == 'active') {
                                $('#editModal #formEntity #active').prop('checked', (response.active == "Y" ? true : false));
                            } else {
                                $('#editModal #formEntity #' + key).val(value);
                            }
                        });                        

                        // controla o botão Salvar conforme o ACL Gate
                        if (response.ACLupdate) { $("#btnSave").show(); } else { $("#btnSave").hide(); }                       
                    },
                    error: function (error) {
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');
                    }
                }); 
            });     

            /*
            * Save currente edition register (create ou update)
            */
            $('.btnSave').on("click", function (e) {
                e.stopImmediatePropagation();

                var activeValue = getAtivoValue();                           // recupera o ativo switch
                var operation = $(this).data('operation');                    // recupera a operação
                // alert(operation);

                // lets join form fields and use index form .get(0)
                const formData = new FormData($('#editModal #formEntity').get(0));
                formData.append('active', activeValue);                       // adiciona o campo ativo ao formData

                $.ajax({
                    type: "POST",
                    url: ( operation == 'insert' ? "{{ url()->current() }}/store" : "{{ url()->current() }}/update"), // ajusta a rota conforme a operação
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        $('#alert .alert-content').html("{{ __('acl.crud.confirmMessageSave') }}<b> " + response.id + '</b>');
                        $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);
                        $('#editModal').modal('hide');
                        $('#datatables').DataTable().ajax.reload(null, false);
                    },
                    error: function (error) {

                        // show errors fields messages from the validator
                        $("#editModal .invalid-feedback").text('').hide();
                        $.each( error.responseJSON.errors, function( key, value ) {
                            $("#editModal #error-" + key ).text(value).show(); 
                        });

                        // show error messages
                        if (error.responseJSON.message.indexOf("1062") != -1) {
                            $('#msgOperacao').html("{{ __('acl.crud.errorMessage1062')}}").show();
                        } else {
                            $('#msgOperacao').html(error.responseJSON.message).show();
                        }
                    }
                });                
            });

            /*
            * Delete button action
            */
            $("#datatables tbody").delegate('tr td .btnDestroy', 'click', function (e) {
                e.stopImmediatePropagation();            

                id = $(this).parents('tr').attr("id");

                // abre Form Modal Bootstrap e pede confirmação da Exclusão do Registro
                $('#msgOperacaoExcluir').text('');
                $('#confirmaExcluirModal .modal-title').html("{{ __('acl.crud.modalDestroyTitle') }} " + entityName);                
                $("#confirmaExcluirModal .modal-body p").html('').html("{{ __('acl.crud.modalDestroyText') }} <b>" + id + '</b>?');
                $('#confirmaExcluirModal').modal('show');

                // se confirmar a Exclusão, exclui o Registro via Ajax
                $('#confirmaExcluirModal').find('.modal-footer #confirm').on('click', function (e) {
                    e.stopImmediatePropagation();

                    $.ajax({
                        type: "POST",
                        url: "{{ url()->current() }}/destroy",
                        data: {"id": id},
                        dataType: 'json',
                        success: function (data) {

                            $("#alert .alert-content").html("{{ __('acl.crud.confirmMessageDestroy') }} <b>" + id + '</b>');
                            $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);
                            $('#confirmaExcluirModal').modal('hide');
                            $('#datatables').DataTable().ajax.reload(null, false);
                        },
                        error: function (error) {
                            
                            if (ERROR_HTTP_STATUS.has(error.status)) {
                                window.location.href = "{{ url('/login') }}";
                                return;
                            } 

                            // show errors messages
                            $('#msgOperacaoExcluir').html(error.responseJSON.message).show();
                            // if(error.responseJSON.message.indexOf("1451") != -1) {
                            //     $('#msgOperacaoExcluir').html("{{ __('acl.crud.errorMessage1451') }}").show();
                            // } else {
                            //     $('#msgOperacaoExcluir').html(error.responseJSON.message).show();
                            // }
                        }
                    });
                });
            }); 

            /**
             * põe o foco no primeiro campo do modal
             */
            $('body').on('shown.bs.modal', '#editModal', function () {
                $('#name').focus();
            })      
            
            /*
            * convert active checkbox to 'Y' : 'N'
            */
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