@extends('adminlte::page')

@section('title', 'Lista de Usuários')

@section('content')
    <div id="app">
        <user-list></user-list>
    </div> 
    <h1>Aqui</h1> 
@endsection

@push('js')
    <script src="{{ mix('resources/js/app.js') }}"></script>
@endpush
