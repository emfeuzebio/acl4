@extends('adminlte::page')

@section('title', 'DataTables Example')

@section('content_header')
    <h1>DataTables Example</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
        <button id="btnRefresh" class="btn btn-default btn-sm btnRefresh" data-toggle="tooltip" title="Atualizar a tabela (Alt+R)">Refresh</button>
            <table id="example" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Age</th>
                        <th>Start Date</th>
                        <th>Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['position'] }}</td>
                            <td>{{ $row['age'] }}</td>
                            <td>{{ $row['start_date'] }}</td>
                            <td>{{ $row['salary'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.css') }}">
@stop

@section('js')
    <!-- DataTables JS -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>

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

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        });
    </script>
@stop
