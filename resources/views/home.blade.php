@extends('layouts.app')

@section('title', __(config('app.name')) . ' Dashboard v1')

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <style>

        .text-cd {
            font-size: 11px;      
            letter-spacing: -0.7px; 
        }

    </style>
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="m-0 text-dark col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/home">Home</a></li>
                <li class="breadcrumb-item ">Dashboard v1</li>
            </ol>
        </div>
    </div>    
@stop 

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {!! session('error') !!}
    </div>
@endif

@section('content')

  <!-- Mantém um limite máximo de largura (bom para evitar layouts muito largos). -->
  <div class="container">

  <!-- Ocupa 100% da largura da tela, mas mantém espaçamento interno. -->
  <!-- <div class="container-fluid"> -->

    <!-- Cards -->
    <div class="row">

        <div class="col-md-2 col-sm-6 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>2</h3>
                    <p>{{ __('acl.dictionary.organizations') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="/organization" class="small-box-footer">
                {{ __('acl.dictionary.moreInfor') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>  
        </div>

        <div class="col-md-2 col-sm-6 col-12">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>3</h3>
                    <p>{{ __('acl.dictionary.systems') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-desktop"></i>
                </div>
                <a href="/system" class="small-box-footer">
                {{ __('acl.dictionary.moreInfor') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>  
        </div>

        <div class="col-md-2 col-sm-6 col-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>11</h3>
                    <p>{{ __('acl.dictionary.entities') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cube"></i>
                </div>
                <a href="/entity" class="small-box-footer">
                {{ __('acl.dictionary.moreInfor') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>  
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>8</h3>
                    <p>{{ __('acl.dictionary.roles') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <a href="/profile" class="small-box-footer">
                {{ __('acl.dictionary.moreInfor') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>  
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>5</h3>
                    <p>{{ __('acl.dictionary.users') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="/user" class="small-box-footer">
                {{ __('acl.dictionary.moreInfor') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>  
        </div>

        
        

    </div>

    <!-- List Tokens -->
    <div class="row">
      <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('acl.dictionary.listTokens') }}</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
              </div>

            </div>
            <div class="card-body">
              <div class="table-responsive col-md-12">
                  <table id="tbtListTokens" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
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

    <!-- List Logins -->
    <div class="row">
      <div class="col-12">
        <div class="card">
            <div class="card-header border-transparent">
              <h3 class="card-title">{{ __('acl.dictionary.listLogins') }}</h3>
              <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table m-0">
                  <thead>
                  <tr>
                    <th>User ID</th>
                    <th>IP</th>
                    <th>Status</th>
                    <th>Browser</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                    <td>Call of Duty IV</td>
                    <td><span class="badge badge-success">Shipped</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="badge badge-warning">Pending</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>iPhone 6 Plus</td>
                    <td><span class="badge badge-danger">Delivered</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="badge badge-info">Processing</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00c0ef" data-height="20">90,80,-90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="badge badge-warning">Pending</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>iPhone 6 Plus</td>
                    <td><span class="badge badge-danger">Delivered</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                    <td>Call of Duty IV</td>
                    <td><span class="badge badge-success">Shipped</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <div class="card-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">{{ __('acl.dictionary.showAllLogins') }}</a>
            </div>
        </div>
      </div>
    </div>

  </div>

@stop

@section('js')

    <!-- JS da própria página blade -->
    <script type="text/javascript">

        $(document).ready(function() {

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
            $('#tbtListTokens').DataTable({
                processing: true,
                responsive: true,
                autoWidth: true,
                order: [ 0, 'desc' ],
                lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "{{ __('acl.crud.todos')}}"]], 
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",
                    url: "api/auth/listTokens",
                    dataSrc: '',  // dispense the data object
                },   
                rowId: 'id',    // set line id <tr id=""> as the id columns field 
                columns: [ 
                    {"data": "id",        "class": "dt-right",  "width": "10px",  "title": "#"},
                    {"data": "user.name",   "class": "dt-left",   "width": "100px", "title": "{{ __('acl.dashboard.columns.user-name') }}",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    {"data": "ip",        "class": "dt-center", "width": "50px",  "title": "{{ __('acl.dashboard.columns.ip-name') }}"},
                    {"data": "browser",   "class": "dt-left",   "width": "auto",  "title": "{{ __('acl.dashboard.columns.browser-name') }}",
                        render: function (data) { return '<span class="text-cd">' + data + '</span>';}},
                    {"data": "created_at","class": "dt-left",   "width": "100px", "title": "{{ __('acl.dashboard.columns.create_at-name') }}",
                        render: function (data) { return '<span class="text-cd">' + data + '</span>';}},
                    {"data": "expires_at","class": "dt-left",   "width": "100px", "title": "{{ __('acl.dashboard.columns.expire_at-name') }}",
                      render: function (data) { return '<span class="text-xs">' + data + '</span>';}},
                    {"data": "status",    "class": "dt-center", "width": "70px",  "title": "{{ __('acl.dashboard.columns.status-name') }}",
                      render: function (data, type, row) { 

                        switch (row.status) {
                          case 'active':
                            return '<span class="badge badge-success">Active</span>';
                          case 'refreshed':
                            return '<span class="badge badge-success">Refreshed</span>';
                          case 'expired':
                            return '<span class="badge badge-danger">Expired</span>';
                          case 'invalidated':
                            return '<span class="badge badge-danger">Invalidated</span>';
                          case 'revoked':
                            return '<span class="badge badge-warning">Revoked</span>';
                          default:
                          return '<span class="badge badge-warning">unnowned</span>';
                        }                        
                      }
                    },
                    {"data": null, "actions": "", "orderable": false, "class": "dt-center", "width": "120px", "title": "{{ __('acl.dashboard.columns.actions-name') }}",
                        render: function (data, type, row) { 
                          
                            btnRevoke = '';
                            btnRefreshTkn = '';

                            // button Show control
                            // if (row.authorizations.includes(entity + '.show')) {
                              btnRevoke = '<button type="button" class="btnRevoke btn btn-danger btn-xs" data-toggle="tooltip" title="{{ __('acl.crud.btnRevokeTip') }}">{{ __('acl.crud.btnRevoke') }}</button> ';
                            // }
                            btnRefreshTkn = '<button type="button" class="btnRefreshTkn btn btn-success btn-xs" data-toggle="tooltip" title="">Renovar</button> ';
                          
                          return btnRevoke + btnRefreshTkn;
                          // return btnEdit + btnDestroy; 
                        }
                    },
                ],
            });


            /*
            * Delete Route: btnDeleteRout . tblActionsBody
            */
            $("#tbtListTokens").delegate('tr td .btnRevoke', 'click', function (e) {
                e.stopImmediatePropagation();   

                id = $(this).parents('tr').attr('id');
                actionName = $(this).parents('tr').find('td:eq(2)').text();
                // alert('btnDeleteRout: ' + id + ' actionName: ' + actionName);

                //se confirmar a Exclusão, exclui o Registro via Ajax
                
                    $.ajax({
                        type: "POST",
                        url: "{{ url("api/auth/revoke") }}", 
                        data: {"tokenId": id},
                        dataType: 'json',
                        success: function (response) {
                            $('#tbtListTokens').DataTable().ajax.reload(null, false);
                            $('#msgOperationAction').removeClass().addClass('alert alert-success').html('Excluiu a Ação: <b>' + response.action + '</b> com sucesso.').show().delay(5000).fadeOut(1000);
                        },
                        error: function (error) {
                            if (ERROR_HTTP_STATUS.has(error.status)) { window.location.href = "{{ url('/login') }}"; return; } 
                            $('#alertModal .modal-body').html(error.responseJSON.message)
                            $('#alertModal').modal('show');
                        }
                    });
                
            });                  


            // console.log("Hi, I'm using the Laravel-AdminLTE package!");
            // Teste de funcionamento do JQuery
            var teste = $('#teste').val();
            console.log("JQuery:" + teste);

        });            

    </script>

@stop 