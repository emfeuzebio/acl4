@extends('adminlte::page')

@section('content_header')

    <div class="row mb-2">
        <div class="m-0 text-dark col-sm-6">
            <h1></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/home">Home</a></li>
                <!-- <li class="breadcrumb-item ">Administração</li> -->
                <!-- <li class="breadcrumb-item">Cadastros</li> -->
                <li class="breadcrumb-item active">Organizações</li>
            </ol>
        </div>
    </div>

    <style>
        .dt-center {
            text-align: center !important;
        }       
    </style>    
@stop

@section('content')

    <!-- DataTables de Dados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4 text-left h5"><b>Cadastro da Organização Militar</b></div>
                        
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
                            <button id="btnNovo" class="btn btn-success btn-sm btnInserirNovo" style="display: none;" data-toggle="tooltip" title="Adicionar um novo registro (Alt+N)" >Inserir Novo</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="datatables" class="table table-striped table-bordered table-hover table-sm compact" style="width:100%">
                        <thead></thead>
                        <tbody></tbody>
                        <tfoot></tfoot>                
                    </table>                 
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Registro -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel">Modal title</h4>
                <button type="button" class="close" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#editarModal').modal('hide');">&times;</button>
            </div>
            <div class="modal-body">

                <form id="formEntity" name="formEntity"  action="javascript:void(0)" 
                    class="form-horizontal" method="post">

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

    <!-- modal excluir registro -->
    <div class="modal fade" id="confirmaExcluirModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-sm" role="document">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-toggle="tooltip" title="Cancelar a operação (Esc ou Alt+C)" onClick="$('#msgOperacaoExcluir').text('');$('#confirmaExcluirModal').modal('hide');">Cancelar</button>
                    <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Confirmar a Exclusão" id="confirm">Excluir</button>
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
            $('.invalid-feedback').text('').hide();
            // alert('Fechou Modal');
        });  

    </script>      


    <!-- script de comportamento da página -->
    <script type="text/javascript">

        $(document).ready(function () {

            var id = '';
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
                // serverSide: true,
                processing: true,
                responsive: true,
                autoWidth: true,
                // order: [ 0, 'desc' ],
                lengthMenu: [[5, 10, 15, 30, 50, -1], [5, 10, 15, 30, 50, "Todos"]], 
                language: { url: "{{ asset('vendor/datatables/DataTables.pt_BR.json') }}" },
                ajax: {
                    type: "GET",
                    url: "organizacao",                             // rota
                    // dataSrc: '',                                 // '' descarta a necessidade do data[] mas precisa estar de acordo com o Controller
                    // data: { param1: 'x' },                       // enviar parametro fixo via GET para o Controller
                    data: function(param) {                         // enviar parametro dinâmico via GET para o Controller
                        param.tipo = 'tipo';                        // Adiciona o valor do campo #tipo aos parâmetros da requisição
                        param.forma = 'forma';                      // Adiciona o valor do campo #forma aos parâmetros da requisição
                    },
                    dataSrc: function (json) {
                        let autorizacoes = json.autorizacoes;       // Rotas autorizadas
                        // console.log(autorizacoes);                  // Rotas autorizadas

                        // controle do botão Inserir Novo
                        if (json.autorizacoes.includes('organizacao.store')) { $("#btnInsertNew").show(); } else { $("#btnInsertNew").hide(); }

                        // controle do botão Salvar do Modal de Edição
                        if (json.autorizacoes.includes('organizacao.update')) { $("#btnSalvar").show(); } else { $("#btnSalvar").hide(); }

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
                    {"data": "nome", "name": "organizacaos.nome", "class": "dt-left", "title": "Nome",
                        render: function (data) { return '<b>' + data + '</b>';}},
                    {"data": "sigla", "name": "organizacaos.sigla", "class": "dt-left", "title": "Sigla"},
                    {"data": "descricao", "name": "organizacaos.descricao", "class": "dt-left", "title": "Descrição"},
                    {"data": "ativo", "name": "organizacaos.ativo", "class": "dt-center", "title": "Ativo",  
                        render: function (data) { return '<span class="' + ( data == 'SIM' ? 'text-primary' : 'text-danger') + '">' + data + '</span>';}
                    },
                    {"data": null, "botoes": "", "orderable": false, "class": "dt-center", "title": "Ações", "width": "80px",
                        render: function (data, type, row) { 

                            btnEditar = '';                 // esconde botoes
                            btnExcluir = '';                // esconde botoes
                            // console.log(data); 

                            // controle botão Ver
                            if (row.autorizacoes.includes('organizacao.show')) {
                                btnEditar = '<button type="button" class="btnEditar btn btn-info btn-xs" data-operacao="ver" data-toggle="tooltip" title="Ver o registro atual">Ver</button> ';
                            }

                            // // controle botão Editar
                            if (row.autorizacoes.includes('organizacao.update')) {
                                btnEditar = '<button type="button" class="btnEditar btn btn-primary btn-xs" data-operacao="salvar" data-toggle="tooltip" title="Editar o registro atual">Editar</button> ';
                            }

                            // // controle botão Excluir
                            if (row.autorizacoes.includes('organizacao.destroy')) {
                                btnExcluir = '<button type="button" class="btnExcluir btn btn-danger btn-xs" data-operacao="excluir" data-toggle="tooltip" title="Excluir o registro atual">Excluir</button> ';
                            }

                            return btnEditar + btnExcluir; 
                        }
                    },
                ],
            });

            /*
            * Inserir um Novo registro
            */
            $('#btnNovo').on("click", function (e) {
                e.stopImmediatePropagation();

                $('#editarModal #form-group-id').hide();                            // esconde o ID
                $('#formEntity').trigger("reset");                                  // limpa mensagens de erro
                $('#editarModal #modalLabel').html('Inserir nova Organização');     // título do modal
                $(".invalid-feedback").text('').hide();                             // hide all error displayed
                $("#btnSalvar").hide();                                             // esconde o btn Salvar
                $("#btnInserir").show();                                            // mostra o btn Inserir
                $('#formEntity #ativo').prop('checked', true);                      // default SIM
                $('#editarModal').modal('show');                                    // show modal 
            });              

            /*
            * Editar ou Ver um registro (show)
            */
            $("#datatables tbody").delegate('tr td .btnEditar', 'click', function (e) {
                e.stopImmediatePropagation();            

                const id = $(this).parents('tr').attr("id");

                $.ajax({
                    type: "GET",
                    url: "organizacao/show", 
                    data: { "id": id },
                    dataType: 'json',
                    success: function (data) {
                        $('#modalLabel').html('Editar Organização');
                        $(".invalid-feedback").text('').hide();     // limpa todas as mensagens de erros dos campos
                        $('#form-group-id').show();                 // sendo uma edição mostra o ID do registro
                        $('#editarModal').modal('show');            // mostra o modal de edição de dados
                        $("#btnInserir").hide();                    // mostra o btn Inserir

                        // carrega os dados nos campos.
                        $('#formEntity #id').val(data.id);
                        $('#formEntity #sigla').val(data.sigla);
                        $('#formEntity #nome').val(data.nome);
                        $('#formEntity #descricao').val(data.descricao);
                        $('#formEntity #ativo').prop('checked', (data.ativo == "SIM" ? true : false));

                        // controla o botão Salvar conforme o ACL Gate
                        if (data.ACLupdate) { $("#btnSalvar").show(); } else { $("#btnSalvar").hide(); }
                    },
                    error: function (error) {
                        $('#alertModal .modal-body').html(error.responseJSON.message)
                        $('#alertModal').modal('show');
                    }
                }); 
            });           

            /*
            * Excluir um registro (destroy)
            */
            $("#datatables tbody").delegate('tr td .btnExcluir', 'click', function (e) {
                e.stopImmediatePropagation();            

                const id = $(this).parents('tr').attr("id");
                // $('#editarModal').modal('show');                                    // show modal 

                // abre Form Modal Bootstrap e pede confirmação da Exclusão do Registro
                $('#msgOperacaoExcluir').text('');
                $("#confirmaExcluirModal .modal-body p").text('Você está certo que deseja Excluir este registro ID: ' + id + '?');
                $('#confirmaExcluirModal').modal('show');

                // se confirmar a Exclusão, exclui o Registro via Ajax
                $('#confirmaExcluirModal').find('.modal-footer #confirm').on('click', function (e) {
                    e.stopImmediatePropagation();

                    $.ajax({
                        type: "POST",
                        url: "organizacao/destroy",
                        data: { "id": id },
                        dataType: 'json',
                        success: function (data) {
                            $("#alert .alert-content").text('Excluiu o registro ID ' + id + ' com sucesso.');
                            $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);
                            $('#confirmaExcluirModal').modal('hide');
                            $('#datatables').DataTable().ajax.reload(null, false);  
                        },
                        error: function (error) {
                            $('#msgOperacaoExcluir').text(error.responseJSON.message).show();
                            if(error.responseJSON.message.indexOf("1451") != -1) {
                                $('#msgOperacaoExcluir').text('Impossível EXCLUIR porque há registros relacionados. (SQL-1451)').show();
                            } else {
                                $('#msgOperacaoExcluir').html(error.responseJSON.message).show();
                            }
                        }
                    });
                });
            });           

            /*
            * Salvar o registro em edição (create ou update)
            */
            $('.btnSalvar').on("click", function (e) {
                e.stopImmediatePropagation();

                // $(".invalid-feedback").text('').hide();                     // hide and clean all erros messages on the form
                var ativoValue = getAtivoValue();                           // recupera o ativo switch
                var operacao = $(this).data('operacao');                    // recupera a operação

                // para reunir os campos do form é necessário usar index do form .get(0)
                const formData = new FormData($('#formEntity').get(0));
                formData.append('ativo', ativoValue);                       // adiciona o campo ativo ao formData

                $.ajax({
                    type: "POST",
                    url: ( operacao == 'inserir' ? 'organizacao/store' : 'organizacao/update'), // ajusta a rota conforme a operação
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $("#alert .alert-content").text('Salvou registro ID ' + response.id + ' com sucesso.');
                        $('#alert').removeClass().addClass('alert alert-success').show().delay(5000).fadeOut(1000);
                        $('#editarModal').modal('hide');
                        $('#datatables').DataTable().ajax.reload(null, false);
                    },
                    error: function (error) {
                        // vamos exibir as mensagens de erro dos campos do validador
                        $("#editarModal .invalid-feedback").text('').hide();
                        $.each( error.responseJSON.errors, function( key, value ) {
                            $("#editarModal #error-" + key ).text(value).show(); 
                        });

                        // exibe mensagem sobre sucesso da operação
                        if (error.responseJSON.message.indexOf("1062") != -1) {
                            $('#msgOperacao').text("Impossível SALVAR! Registro já existe. (SQL-1062)").show();
                        } else {
                            $('#msgOperacao').html(error.responseJSON.message).show();
                        }
                    }
                });                
            });

            // põe o foco no primeiro campo do modal
            $('body').on('shown.bs.modal', '#editarModal', function () {
                $('#nome').focus();
            })

            /*
            * Atualiza a tabela de dados de registros
            */            
            $('#btnRefresh').on("click", function (e) {
                e.stopImmediatePropagation();

                $('#datatables').DataTable().ajax.reload(null, false);    
                $('#alert').trigger('reset').hide();
            });        

            /*
            * Usado para converter o checkbox Ativo
            */
            function getAtivoValue() {
                if ($('input[id="ativo"]:checked').val()) {
                    return 'SIM';
                } else {
                    return 'NÃO';
                }
            }

        });

    </script>    

@endpush