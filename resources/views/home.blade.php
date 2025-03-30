@extends('layouts.app')

@section('title', __(config('app.name')) . ' Dashboard v1')

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
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
                <table id="tabela-tokens" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Token</th>
                            <th>IP</th>
                            <th>Navegador</th>
                            <th>Emitido em</th>
                            <th>Expira em</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>admin</td>
                            <td>eyJhbGciOiJ...9zZWQi</td>
                            <td>192.168.1.1</td>
                            <td>Chrome</td>
                            <td>26/03/2025 14:00</td>
                            <td>26/03/2025 16:00</td>
                            <td><span class="badge badge-success">Válido</span></td>
                        </tr>
                        <tr>
                            <td>user1</td>
                            <td>eyJhbGciOiJ...xNhdXRo</td>
                            <td>203.0.113.45</td>
                            <td>Firefox</td>
                            <td>26/03/2025 13:00</td>
                            <td>26/03/2025 15:00</td>
                            <td><span class="badge badge-danger">Expirado</span></td>
                        </tr>
                        <tr>
                            <td>guest</td>
                            <td>eyJhbGciOiJ...uJpbmZv</td>
                            <td>192.168.2.10</td>
                            <td>Edge</td>
                            <td>26/03/2025 12:30</td>
                            <td>26/03/2025 14:30</td>
                            <td><span class="badge badge-warning">Quase Expirando</span></td>
                        </tr>
                    </tbody>
                </table>
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
            $('#tabela-tokens').DataTable({
                processing: true,
                responsive: true,
                autoWidth: true,
                // order: [ 0, 'desc' ],
                lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "{{ __('acl.crud.todos')}}"]], 
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",
                    url: "api/auth/listTokens",
                },   
                rowId: 'id',    // set line id <tr id=""> as the id columns field 
                columns: [ 
                    {"data": "id", "name": "organization.id", "class": "dt-right", "title": "#"},
                    {"data": "name", "name": "organization.name", "class": "dt-left", "width": "250px", "title": "{{ __('acl.organization.columns.name-name') }}",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    {"data": "acronym", "name": "organization.acronym", "class": "dt-left", "width": "50px", "title": "{{ __('acl.organization.columns.acronym-name') }}"},
                    {"data": "description", "name": "organization.description", "class": "dt-left", "width": "auto", "title": "{{ __('acl.organization.columns.description-name') }}",},
                    
                    // {"data": "active", "name": "organization.active", "class": "dt-center", "width": "50px", "title": "{{ __('acl.organization.columns.active-name') }}",
                    //     render: function (data) { return '<span class="' + ( data == 'Y' ? 'text-primary' : 'text-danger') + '">' + ( data == 'Y' ? '{{ __('acl.crud.columns_data.yes') }}' : '{{ __('acl.crud.columns_data.no') }}' ) + '</span>';}
                    // },
                    // {"data": null, "actions": "", "orderable": false, "class": "dt-center", "width": "100px", "title": "{{ __('acl.organization.columns.actions-name') }}",
                    //     render: function (data, type, row) {  

                    //         btnEdit = '';                 
                    //         btnDestroy = '';                
                    //         // console.log(data); 

                    //         // button Show control
                    //         if (row.authorizations.includes(entity + '.show')) {
                    //             btnEdit = '<button type="button" class="btnEdit btn btn-info btn-xs" data-operation="ver" data-toggle="tooltip" title="{{ __('acl.crud.btnShowTip') }}">{{ __('acl.crud.btnShow') }}</button> ';
                    //         }

                    //         // button Edit control
                    //         if (row.authorizations.includes(entity + '.update')) {
                    //             btnEdit = '<button type="button" class="btnEdit btn btn-primary btn-xs" data-operation="save" data-toggle="tooltip" title="{{ __('acl.crud.btnEditTip') }}">{{ __('acl.crud.btnEdit') }}</button> ';
                    //         }

                    //         // button Destroy control
                    //         if (row.authorizations.includes(entity + '.destroy')) {
                    //             btnDestroy = '<button type="button" class="btnDestroy btn btn-danger btn-xs" data-operation="excluir" data-toggle="tooltip" title="{{ __('acl.crud.btnDestroyTip') }}">{{ __('acl.crud.btnDestroy') }}</button> ';
                    //         }

                    //         return btnEdit + btnDestroy; 
                    //     }
                    // },
                ],
            });



            // console.log("Hi, I'm using the Laravel-AdminLTE package!");
            // Teste de funcionamento do JQuery
            var teste = $('#teste').val();
            console.log("JQuery:" + teste);

        });            

    </script>

@stop 