function Login() {
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            "funcao": "LOGIN",
            "Usuario": $('#Usuario').val(),
            "Senha": $('#Senha').val()
        },
        success: function(data) {
            if(data === "SUCESSO"){
                window.location = "./"
            }
            else{
                $('#erro').html('Credenciais Inválidas')
                $("#erro").fadeOut(2000);
            }
        }
    });
}
function AtualizaTabelaReunioes() {
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            "funcao": "AtualizaReunioes"
        },
        success: function(data) {
            $("#Tabela_Reunioes").html(data);
            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "date-br-pre": function ( a ) {
                    var x;
                    if ( $.trim(a) !== '' ) {
                        var frDatea = $.trim(a).split(' ');
                        var frTimea = (undefined != frDatea[1]) ? frDatea[1].split(':') : [00,00,00];
                        var frDatea2 = frDatea[0].split('/');
                        x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
                    }
                    else {
                        x = Infinity;
                    }
                    return x;
                },
                "date-br-asc": function ( a, b ) {
                    return a - b;
                },
                "date-br-desc": function ( a, b ) {
                    return b - a;
                }
            } );
            $('#listar-usuario').DataTable({
                "order": [[ 0, "asc" ]],
                dom: 'Bfrtip',
                buttons: [
                    'copy',
                    {
                        extend: 'excel',
                        title: 'Relatório Sala de Reuniões'
                    },
                    {
                        extend: 'pdf',
                        title: 'Relatório Sala de Reuniões',

                        customize: function(doc){
                            doc.styles.tableHeader.fillColor =  '#5787D1';
                            doc.styles.title.color =  '#5787D1';
                            doc.content.layout =  'borders';
                            var now = new Date();
                            var jsDate = now.getDate()+'/'+(now.getMonth()+1)+'/'+now.getFullYear();
                            doc['thdead']=(function(){
                                return {
                                    columns: [
                                        {
                                            color:'#aaa'
                                        }
                                    ],
                                    margin: 20,
                                    padding:2
                                }
                            });
                            doc['th']=(function(){
                                return{
                                    columns: [
                                        {
                                            color:red
                                        }

                                    ],
                                    margin: 20,
                                    padding:2
                                }
                            });
                            doc['footer']=(function(page, pages) {
                                return {
                                    columns: [
                                        {
                                            alignment: 'left',
                                            text: ['Gerado em: ', { text: jsDate.toString() }]
                                        },
                                        {
                                            alignment: 'right',
                                            text: ['Página ', { text: page.toString() },  ' de ', { text: pages.toString() }]
                                        }
                                    ],
                                    margin: 20
                                }
                            });
                        }
                    }
                ]
                ,"columnDefs": [
                    {
                        "orderable": false,
                        "targets": 3
                    },
                    {
                        "type": 'date-br',
                        "targets": 0
                    }
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
                    }
                }
            });

        }
    });
}
function BuscaCadastro(ID, FUNCTION) {
    $("#visualiza-modal").modal('show');
    //alert(ID);
    $.ajax({
        type: "POST",
        url: "./ajax.php",
        data: {
            "funcao": 'BuscaCadastro',
            "TIPO": FUNCTION,
            "Pagina": 'eventos',
            "CampoID": 'EvId',
            "ID": ID
        },
        success: function(dados) {
            $("#content_modal").html(dados);
            if(FUNCTION === 'DEL') {
                $("#titulo_do_modal").html('Excluir Reunião');
                $("#content_modal_footer").html('<b>Tem certeza que deseja excluir este registro? </b><button type="button" class="btn btn-warning" onclick="ExcluirRegistro('+ID+');"><span class="glyphicon glyphicon-remove"></span> Excluir</button><button type="button" class="btn btn-danger btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Fechar</button>');
            } else if(FUNCTION === 'SHOW'){
                $("#titulo_do_modal").html('Visualizar Reunião');
                $("#content_modal_footer").html('<button type="button" class="btn btn-danger btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Fechar</button>');
                $('.js-example-basic-multiple').select2();
            }
            else if(FUNCTION === 'EDIT') {
                $("#titulo_do_modal").html('Editar Reunião');
                $("#content_modal_footer").html('<button type="button" class="btn btn-primary" onclick="EnviarCadastroEdit('+ID+');"><span class="glyphicon glyphicon-save"></span> Salvar</button> <button type="button" class="btn btn-danger btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Fechar</button>');
                $('.js-example-basic-multiple').select2();
                $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: {
                        "funcao": "SelectUsers",
                        "ID":ID
                    },
                    success: function(data) {
                        $('#usersedit').val(JSON.parse(data));
                        $('#usersedit').trigger('change'); // Notify any JS components that the value changed
                    }
                });
            } else if(FUNCTION === 'CAD'){
                $("#titulo_do_modal").html('Cadastrar Reunião');
                $("#content_modal_footer").html('<button type="button" class="btn btn-primary" onclick="EnviarCadastro();"><span class="glyphicon glyphicon-save"></span> Salvar</button> <button type="button" class="btn btn-danger btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Fechar</button>');
                $('.js-example-basic-multiple').select2();
            }
        }
    });
}
function EnviarCadastro(id, TIPO){
    var data = document.getElementById("datecad").value;
    var inicio = document.getElementById("iniciocad").value;
    var fim = document.getElementById("fimcad").value;
    var subject = document.getElementById("subject").value;
    var users = $('#userscad').val();
    if(users.length >0){
        users2 = [];
        for(var i = 0;i < users.length ; i++){
            users2.push(Number(users[i]))
        }
        users = users2;
    }

    if(data !== '' && inicio !== '' && fim !== '') {
        $.ajax({
            type: "POST",
            url: "./ajax.php",
            data: {
                "funcao":'ProcuraData',
                "Pagina": 'units',
                "data": data,
                "inicio":inicio,
                "fim":fim
            },
            success: function(dados) {
                json = JSON.parse(dados);
                if(json.situacao === 'SUCESSO'){
                    $.ajax({
                        type: "POST",
                        url: "./ajax.php",
                        data: {
                            "funcao":'CadastrarRegistro',
                            "Pagina": 'reunioes',
                            "TIPO": 'NOVO',
                            "data": data,
                            "inicio": inicio,
                            "fim": fim,
                            "users":users,
                            "subject": subject
                        },
                        success: function(dados) {
                            AtualizaTabelaReunioes();
                            if(TIPO === "NOVO") {
                                $("#myModal").modal('hide');
                            } else {
                                $("#visualiza-modal").modal('hide');
                            }
                        } });
                }
                else{
                    alert("Horário já reservado");
                }
            }
        });
    } else {
        alert("Preencha os campos Obrigatórios");
    }
}
function EnviarCadastroEdit(id, TIPO){
    var data = document.getElementById("dateedit").value;
    var inicio = document.getElementById("inicioedit").value;
    var fim = document.getElementById("fimedit").value;
    var subject = document.getElementById("subjectedit").value;
    var users = $('#usersedit').val();
    if(users.length >0){
        users2 = [];
        for(var i = 0;i < users.length ; i++){
            users2.push(Number(users[i]))
        }
        users = users2;
    }

    if(data !== '' && inicio !== '' && fim !== '') {
        $.ajax({
            type: "POST",
            url: "./ajax.php",
            data: {
                "funcao":'CadastrarRegistro',
                "Pagina": 'reunioes',
                "TIPO": 'EDIT',
                "data": data,
                "inicio": inicio,
                "fim": fim,
                "users":users,
                "ID":id,
                "subject":subject
            },
            success: function(dados) {
                AtualizaTabelaReunioes();
                if(TIPO === "NOVO") {
                    $("#myModal").modal('hide');
                } else {
                    $("#visualiza-modal").modal('hide');
                }
            }
        });
    } else {
        alert("Preencha os campos Obrigatórios");
    }
}
function ExcluirRegistro(ID) {
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            "funcao": "ExcluirRegistro",
            "Pagina":"eventos",
            "CampoRemovido": "EvRemovido",
            "CampoId": "EvId",
            "ID":ID
        },
        success: function(data) {
            $("#Tabela_Reunioes").html(data);
            AtualizaTabelaReunioes();
            $("#visualiza-modal").modal('hide');
        }
    });
}
function VerificaCadReuniao(){
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            "funcao": "PermissaoCadReunioes"
        },
        success: function(data) {
            json = JSON.parse(data);
            if(json.situacao === 'SUCESSO'){
                AtualizaTabelaReunioes();
                $('#ConteudoPrincipalHome2').css('display', 'block');
            }
            else{
                $('#ConteudoPrincipalHome2').css('display', 'none');
            }
        }
    });
}
