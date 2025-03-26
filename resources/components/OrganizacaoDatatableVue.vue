<template>
    <div class="container mt-4">
        <button @click="reloadData" class="btn btn-sm btn-info mb-2">Refresh</button>

        <table id="users-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- O DataTables vai preencher os dados aqui via Ajax -->
            </tbody>
        </table>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import axios from 'axios';

export default {
    name: 'UserList',
    methods: {
        reloadData() {
            // Realiza o reload manual do DataTable
            $('#users-table').DataTable().ajax.reload();
        }
    },
    mounted() {
        // Inicializa o DataTables com Ajax
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'organizacaos', // URL da API que vai retornar os usuários
                type: 'GET',
                dataSrc: (json) => {
                    return json.data; // A resposta deve ter um campo 'data' com os registros
                }
            },
            columns: [
                { data: 'id' },
                { data: 'nome' },
                { data: 'sigla' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button class="btn btn-sm btn-primary">Editar</button>
                                <button class="btn btn-sm btn-danger">Deletar</button>`;
                    }
                }
            ]
        });
    }
};
</script>

<style scoped>
/* Estilos personalizados aqui, se necessário */
</style>
