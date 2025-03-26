@extends('layouts.app')

@section('title', 'ACL Dashboard')

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
                <li class="breadcrumb-item ">Administração</li>
                <li class="breadcrumb-item active">Cadastro</li>
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

@section('content_body')
    <!-- <p style="color: blue;">Welcome to this beautiful admin panel.</p> -->
    <input type="hidden" id="teste" value="Hello JQuery funcionando Ok!">

    <!-- DataTables de Dados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4 text-left h5"><b>Cadastro</b></div>
                        
                        <!--área de mensagens-->
                        <div class="col-md-5 text-left">
                            <div style="padding: 0px;  background-color: transparent;">
                                <div id="alert" class="alert alert-danger" style="margin-bottom: 0px; display: none; padding: 2px 5px 2px 5px;">
                                    <a class="close" onClick="$('.alert').hide()">&times;</a>  
                                    <div class="alert-content">Mensagem</div>
                                </div>
                            </div>                         
                        </div>
                                                
                        <div class="col-md-3 text-right">
                            <button id="btnRefresh" class="btn btn-default btn-sm btnRefresh" data-toggle="tooltip" title="Atualizar a tabela (Alt+R)">Refresh</button>
                            <button id="btnInserirNovo" class="btnInserirNovo btn btn-success btn-sm" data-toggle="tooltip" title="Adicionar um novo registro (Alt+N)" >Inserir Novo</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="datatables" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                        <thead>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Age</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>100</td>
                                <td>Marcos da Silva</td>
                                <td>Imóvel</td>
                            </tr>
                            <tr>
                                <td>101</td>
                                <td>Mauro da Onda</td>
                                <td>Carros</td>
                            </tr>
                            <tr id="10">
                                <td>102</td>
                                <td>Sandra Maria</td>
                                <td>
                                    <button class="btnExcluir btn btn-danger btn-xs"  data-toggle="tooltip" title="Excluir o registro atual">Excluir</button>
                                    <button class="btnEditar  btn btn-primary btn-xs" data-toggle="tooltip" title="Editar o registro atual">Editar</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot></tfoot>                
                    </table>                 
                </div>
            </div>
        </div>
    </div>    

@stop

@section('js')
    <!-- JS da própria página blade -->
    <script type="text/javascript">

        $(document).ready(function() {

            // Lista a tabela de dados de registros    
            // $('#datatables').DataTable({
            //      // serverSide: true,
            //     processing: true,
            //     responsive: true,
            //     autoWidth: true,
            //     // order: [ 0, 'desc' ],
            //     lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "Todos"]], 
            //     ajax: "organization",
            //     // ajax: {
            //     //     type: "GET",
            //     //     url: "{{url("perfil")}}",                             // rota
            //     //     dataSrc: function (json) {
            //     //         let autorizacoes = json.autorizacoes;           // Rotas autorizadas

            //     //         // controle do botão Inserir Novo
            //     //         if (json.autorizacoes.includes('perfil.store')) { $("#btnInserirNovo").show(); } else { $("#btnInserirNovo").hide(); }

            //     //         // controle do botão Salvar do Modal de Edição
            //     //         if (json.autorizacoes.includes('perfil.update')) { $("#btnSalvar").show(); } else { $("#btnSalvar").hide(); }
                        
            //     //         return json.data;                           // Retorna lista de dados para o DataTables
            //     //     },                    
            //     // },                 
            //     rowId: 'id',
            //     columns: [
            //         {"data": "id", "name": "organization.id", "class": "dt-right", "title": "#"},
            //         {"data": "name", "name": "organization.nome", "class": "dt-left", "title": "Nome",
            //             render: function (data) { return '<b>' + data + '</b>';}},
            //         {"data": "acronym", "name": "organization.sigla", "class": "dt-left", "title": "Sigla"},
            //         {"data": "description", "name": "organization.descricao", "class": "dt-left", "title": "Descrição"},
            //         {"data": "active", "name": "organization.ativo", "class": "dt-center", "title": "Ativo",  
            //             render: function (data) { return '<span class="' + ( data == 'SIM' ? 'text-primary' : 'text-danger') + '">' + data + '</span>';}
            //         },
            //         {"data": "id", "botoes": "", "orderable": false, "class": "dt-center", "title": "Ações", "width": "80px", 
            //             render: function(data, type, row) {

            //                 btnEditar = '';                 // esconde botoes
            //                 btnExcluir = '';                // esconde botoes

            //                 // controle botão Ver
            //                 if (row.autorizations.includes('perfil.show')) {
            //                     btnEditar = '<button class="btnEditar btn btn-primary btn-xs" data-operacao="ver" data-toggle="tooltip" title="Ver o registro atual">Ver</button> ';
            //                 }

            //                 // controle botão Editar
            //                 if (row.autorizations.includes('perfil.update')) {
            //                     btnEditar = '<button class="btnEditar btn btn-primary btn-xs" data-operacao="salvar" data-toggle="tooltip" title="Editar o registro atual">Editar</button> ';
            //                 }

            //                 // // controle botão Excluir
            //                 if (row.autorizations.includes('perfil.destroy')) {
            //                     btnExcluir = '<button class="btnExcluir btn btn-danger btn-xs" data-toggle="tooltip" title="Excluir o registro atual">Excluir</button> ';
            //                 }

            //                 return btnEditar + btnExcluir; 
            //             }                
            //         }                
            //     ]                
            // });           

            // console.log("Hi, I'm using the Laravel-AdminLTE package!");
            // Teste de funcionamento do JQuery
            var teste = $('#teste').val();
            console.log("JQuery:" + teste);

        });            

    </script>

@stop 