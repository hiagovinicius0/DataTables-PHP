<link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="./DataTables/datatables.min.css">
<script type="text/javascript" src="./js/jquery.js"></script>
<script type="text/javascript" src="./DataTables/datatables.min.js"></script>
<script type="text/javascript" src="./js/bootstrap.js"></script>

<div class="row" id="ConteudoPrincipalHome" >
    <div class="col-sm-1"></div>
    <div class="col-sm-10">
        <div class="panel panel-default">
            <div class="panel-heading"><b>Utilizando DataTables</b></div>
            <div class="panel-body" style="width:100%;height: 600px;overflow: auto;">
                <br>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="TabelaUsuarios">
                            <table id="listar-usuario" class="display" style="width:100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Sobrenome</th>
                                    <th>Cidade</th>
                                    <th>Endereço</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-1"></div>
</div>
<script src="./js/bootstrap.js"></script>
<link href="https://cdn.datatables.net/v/dt/dt-1.10.18/b-1.5.6/b-html5-1.5.6/datatables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/v/dt/dt-1.10.18/b-1.5.6/b-html5-1.5.6/datatables.min.js"></script>
<script>
    function AtualizaTabelaUsuarios() {
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#listar-usuario')) {
                $('#listar-usuario').DataTable().destroy();
            }
            $('#listar-usuario').DataTable({
                "processing": true,
                "serverSide": true,
                "stateSave":true, //Se ativo, quando atualiza a página, ele retorna sem perder o cache
                "ajax": {
                    "url": "./server.php",
                    "type": "POST"
                },
                /*"order": [[ 4, "desc" ]],  Caso quiser mudar a ordem de alguma coluna como padrão*/
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4 ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4 ]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4 ]
                        }
                    }
                ],
                "columnDefs": [
                    /*{ "orderable": false, "targets": 5 } Caso queira deixar uma coluna sem a opção de ordenar (fixa)*/
                ],
                "language": {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "_MENU_ resultados por página",
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": "Pesquisar",
                    "oPaginate": {
                        "sNext": "Próximo",
                        "sPrevious": "Anterior",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                }
            });
        });
    }
    $(document).ready (function(){
        AtualizaTabelaUsuarios();
    });
</script>