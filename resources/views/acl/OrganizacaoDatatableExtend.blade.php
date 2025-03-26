@extends('layouts.app')

@section('title', __('acl.organization.title'))

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
    <input type="hidden" id="teste" value="Hello JQuery funcionando Ok!">

    @section('table_title', __('acl.' . request()->path() . '.table_title'))
    
    <!-- Modal Editar Registro -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#editarModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">

                    <form id="formEntity" name="formEntity"  action="javascript:void(0)" class="form-horizontal" method="post">

                        <div class="form-group" id="form-group-id">
                            <label class="form-label">ID</label>
                            <input class="form-control" value="" type="text" id="id" name="id" placeholder="" readonly>
                        </div>                         
                        
                        <div class="form-group">
                            <label class="form-label">Nome</label>
                            <input class="form-control" value="" type="text" id="nome" name="nome" placeholder="1º Batalhão de Infantaria" data-toggle="tooltip" title="Digite o Nome da Organização" >
                            <div id="error-nome" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sigla</label>
                            <input class="form-control" value="" type="text" id="sigla" name="sigla" placeholder="1º Btl Inf" data-toggle="tooltip" title="Digite a sigla da Organização" >
                            <div id="error-sigla" class="error invalid-feedback" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group input-group-sm">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" placeholder="1º Batalhão de Infantaria" data-toggle="tooltip" title="Informe a Descrição do Perfil de Acesso" rows="4"></textarea>
                            <div id="error-descricao" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group input-group-sm">
                            <label class="form-label" data-toggle="tooltip" title="Marcar se o Perfil de Acesso está Ativo">Ativo</label>
                            <label class="switch">
                                <input type="checkbox" id="ativo" name="ativo" class="switch-input" data-toggle="tooltip" title="Marcar se  o Perfil de Acesso está Ativo">
                                <span class="switch-label" data-on="SIM" data-off="NÃO"></span>
                                <span class="switch-handle"></span>
                            </label>
                            <div id="error-ativo" class="error invalid-feedback" style="display: none;"></div>
                        </div>

                    </form>        

                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <label id="msgOperacao" class="error invalid-feedback" style="color: red; display: none; font-size: 12px;"></label> 
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#editarModal').modal('hide');">Cancelar</button>
                        <button type="button" class="btn btn-primary btnSalvar" style="display: none;" id="btnSalvar" data-operacao="salvar" data-toggle="tooltip" title="Salvar o registro (Alt+S)">Salvar</button>
                        <button type="button" class="btn btn-success btnSalvar" style="display: none;" id="btnInserir" data-operacao="inserir" data-toggle="tooltip" title="Inserir o registro (Alt+S)">Inserir</button>
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


    <!-- script de comportamento da página -->
    <script type="text/javascript">

        $(document).ready(function () {

            var id = '';
            var entidade = 'organizacao';
            var autorizacoes = '';
            var btnInserir = '';
            var btnEditar = '';
            var btnExcluir = '';

            /** 
             * gerencia o X-CSRF-TOKEN e redireciona para login caso não autenticado
             */
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },  // valida o X-CSRF-TOKEN
                statusCode: { 401: function() { window.location.href = "/login";} },        // 401-UNAUTHORIZED redireciona para login
            });

            /*
            * Lista a tabela de dados de registros
            */
            $('#datatables').DataTable({
                // serverSide: true,                                // requer instalação do YajraDataTables
                processing: true,
                responsive: true,
                autoWidth: true,
                // order: [ 0, 'desc' ],
                lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "Todos"]], 
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",
                    url: "{{ url()->current() }}",                  // rota tem o nome da entidade
                    // data: { param1: 'x' },                       // enviar parametro fixo via GET para o Controller
                    data: function(param) {                         // enviar parametro dinâmico via GET para o Controller
                        param.tipo = 'tipo';                        // Adiciona o valor do campo #tipo aos parâmetros da requisição
                        param.forma = 'forma';                      // Adiciona o valor do campo #forma aos parâmetros da requisição
                    },
                    // dataSrc: '',                                 // '' descarta a necessidade do data[] mas precisa estar de acordo com o Controller
                    dataSrc: function (json) {
                        let autorizacoes = json.autorizacoes;       // Rotas autorizadas
                        // console.log(autorizacoes);                  // Rotas autorizadas

                        // controle do botão Inserir Novo
                        if (json.autorizacoes.includes(entidade + '.store')) { $("#btnInsertNew").show(); } else { $("#btnInsertNew").hide(); }

                        // controle do botão Salvar do Modal de Edição
                        if (json.autorizacoes.includes(entidade + '.update')) { $("#btnSalvar").show(); } else { $("#btnSalvar").hide(); }

                        return json.data;                           // Retorna lista de dados para o DataTables
                    },
                    error: function(xhr, status, error) {
                        // para evitar erros visívies no  DataTables 
                        if (xhr.status == 401) { window.location.href = "{{ url('/login')}}";}     // 401-UNAUTHORIZED envia para login
                        if (xhr.status == 403) { window.location.href = "{{ url('/home') }}";}     // 403-FORBIDDEN envia para home
                    }
                },   
                rowId: 'id',    // seta o id="" da TR como sendo o campo: id
                columns: [
                    {"data": "id", "name": "organizacaos.id", "class": "dt-right", "title": "#"},
                    {"data": "nome", "name": "organizacaos.nome", "class": "dt-left", "title": "Nome", "width": "200px",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    {"data": "sigla", "name": "organizacaos.sigla", "class": "dt-left", "title": "Sigla"},
                    {"data": "descricao", "name": "organizacaos.descricao", "class": "dt-left", "title": "Descrição", "width": "auto",},
                    {"data": "ativo", "name": "organizacaos.ativo", "class": "dt-center", "title": "Ativo", "width": "50px",
                        render: function (data) { return '<span class="' + ( data == 'SIM' ? 'text-primary' : 'text-danger') + '">' + data + '</span>';}
                    },
                    {"data": null, "botoes": "", "orderable": false, "class": "dt-center", "title": "Ações", "width": "100px",
                        render: function (data, type, row) { 

                            btnEditar = '';                 // esconde botoes
                            btnExcluir = '';                // esconde botoes
                            // console.log(data); 

                            // controle botão Ver
                            if (row.autorizacoes.includes(entidade + '.show')) {
                                btnEditar = '<button type="button" class="btnEditar btn btn-info btn-xs" data-operacao="ver" data-toggle="tooltip" title="Ver o registro atual">Ver</button> ';
                            }

                            // // controle botão Editar
                            if (row.autorizacoes.includes(entidade + '.update')) {
                                btnEditar = '<button type="button" class="btnEditar btn btn-primary btn-xs" data-operacao="salvar" data-toggle="tooltip" title="Editar o registro atual">Editar</button> ';
                            }

                            // // controle botão Excluir
                            if (row.autorizacoes.includes(entidade + '.destroy')) {
                                btnExcluir = '<button type="button" class="btnExcluir btn btn-danger btn-xs" data-operacao="excluir" data-toggle="tooltip" title="Excluir o registro atual">Excluir</button> ';
                            }

                            return btnEditar + btnExcluir; 
                        }
                    },
                ],
            });

        });

    </script>    

@endpush