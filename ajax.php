<?php
require_once './inc/db_connect.php';
require_once './inc/queries.php';

if(isset($_POST['funcao'])) {
    if($_POST['funcao'] == 'LOGIN') {
        $QUERY = "SELECT * FROM Usuarios WHERE Login = '" . $_POST['Usuario'] . "' AND Senha = '" . $_POST['Senha'] . "' AND Removido = 0";
        $resultado = mysqli_query($con, $QUERY);
        $qtd_resultado = mysqli_num_rows($resultado);
        $linha = mysqli_fetch_array($resultado);
        if ($qtd_resultado == 1) {
            session_start();
            $expira = time() + 3600 * 24;
            setCookie("reuniao[IdUsuario]", $linha['IdUsuario'], $expira);
            setCookie("reuniao[Nome]", $linha['Nome'], $expira);
            echo 'SUCESSO';
        } else {
            echo 'ERRO';
        }
    }
	elseif($_POST['funcao'] == "AtualizaReunioes") {
	    $QUERY = "SELECT EvId, EvDate, EvStart, EvEnd,EvSubject, Nome FROM eventos, Usuarios WHERE EvRemovido = 0 AND EvUId = Usuarios.IdUsuario AND EvDate >= '".date("Y-m-d")."' ORDER BY EvDate , EvStart ASC";
	    $resultado = mysqli_query($con, $QUERY);
	    $total_linhas = mysqli_num_rows($resultado);
        if($total_linhas > 0){
            echo "<table class='table display' style='font-size: 12px;' id='listar-usuario'>
                    <thead>
                        <tr>
                            <th style='width: 90px'>Data</th>
                            <th style='width: 100px;'>Horário</th>
                            <th>Assunto</th>
                            <th></th>
                        </tr>                   
                    </thead>
                    <tbody>
            ";
            while ($linha = mysqli_fetch_array($resultado)){
                $editar = permissao($con, 0, $linha['EvId']);
                $excluir = permissao($con, 1, $linha['EvId']);
                echo "<tr>";
                echo "<td>". date('d/m/Y', strtotime($linha['EvDate']))."</td>";
                echo "<td>".date('H:i', strtotime($linha['EvStart']))."/".date('H:i', strtotime($linha['EvEnd']))."</td>";
                echo "<td>".$linha['EvSubject']."</td>";
                echo "<td>";
                echo "<a class='btn btn-success glyphicon glyphicon-search btn-xs' href='#' onclick='BuscaCadastro({$linha['EvId']}, \"SHOW\")' title='Visualizar'></a>";
                if($editar){
                    echo "&nbsp;<a class='btn btn-warning btn-xs glyphicon glyphicon-pencil' onclick='BuscaCadastro({$linha['EvId']}, \"EDIT\")' title='Editar'></a>";
                }
                if($excluir){
                    echo "&nbsp;<a class='btn btn-danger btn-xs glyphicon glyphicon-remove' onclick='BuscaCadastro({$linha['EvId']}, \"DEL\")' title='Excluir'></a>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
        else{
            echo "<h5>Não há reuniões cadastradas</h5>";
        }
    }
	elseif ($_POST['funcao'] == "BuscaCadastro"){
	    if($_POST['Pagina'] == "eventos"){
	        if($_POST['TIPO'] == "CAD"){
	            echo '
                <div class="row">
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="datecad">Data*:</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="datecad" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="iniciocad">Início*:</label>
                        <div class="col-sm-10">
                            <input type="time" class="form-control" id="iniciocad" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="fimcad">Fim*:</label>
                        <div class="col-sm-10">
                            <input type="time" class="form-control" id="fimcad" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="subject">Assunto:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="subject">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="userscad">Usuários:</label>
                        <div class="col-sm-10">
                        <select class="js-example-basic-multiple" id="userscad" multiple="multiple" style="width:100%;display:block;box-sizing:border-box">';
                            $QUERYPESSOAS = "SELECT IdUsuario, Nome FROM Usuarios WHERE Removido = 0 AND IdUsuario != ".$_COOKIE['reuniao']['IdUsuario']." ORDER BY Nome ASC";
                            $resultado = mysqli_query($con, $QUERYPESSOAS);
                            while($linha = mysqli_fetch_array($resultado)){
                                echo "<option value='".$linha['IdUsuario']."'>".$linha['Nome']."</option>";
                            }
                        echo '</select>
                        </div>
                    </div>
                </div>';
            }
	        elseif($_POST['TIPO'] == "SHOW"){
	            $linha = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM eventos, Usuarios WHERE EvId = ".$_POST['ID']." AND eventos.EvUId = Usuarios.IdUsuario"));
                echo '
                <div class="row">
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-3" for="datecad">Data*:</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="datecad" placeholder="Enter email" value="'.$linha['EvDate'].'" readonly>
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-3" for="iniciocad">Início*:</label>
                        <div class="col-sm-9">
                            <input type="time" class="form-control" id="iniciocad" placeholder="Enter email" value="'.$linha['EvStart'].'" readonly>
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-3" for="fimcad">Fim*:</label>
                        <div class="col-sm-9">
                            <input type="time" class="form-control" id="fimcad" placeholder="Enter email" value="'.$linha['EvEnd'].'" readonly>
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-3" for="fimcad">Assunto*:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="fimcad" value="'.$linha['EvSubject'].'" readonly>
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-3" for="fimcad">Responsável*:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="fimcad" placeholder="Enter email" value="'.$linha['Nome'].'" readonly>
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-3" for="usershow">Usuários:</label>
                        <div class="col-sm-9">';

                $QUERYPESSOAS = mysqli_fetch_assoc(mysqli_query($con,"SELECT GROUP_CONCAT(Nome) AS NOME FROM admin_eventos, Usuarios WHERE EvId = ".$_POST['ID']." AND UId = Usuarios.IdUsuario"));
                echo "<input type='text' id='usershow' value='".$QUERYPESSOAS['NOME']."' class='form-control' readonly>";
                echo '</select>
                        </div>
                    </div>
                </div>';
            }
	        elseif($_POST['TIPO'] == "EDIT"){
                $linha = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM eventos WHERE EvId = ".$_POST['ID']));
                echo '
                <div class="row">
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="dateedit">Data*:</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="dateedit" value="'.$linha['EvDate'].'" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="inicioedit">Início*:</label>
                        <div class="col-sm-10">
                            <input type="time" class="form-control" id="inicioedit" placeholder="Enter email" value="'.$linha['EvStart'].'">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="fimedit">Fim:</label>
                        <div class="col-sm-10">
                            <input type="time" class="form-control" id="fimedit" value="'.$linha['EvEnd'].'">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="subjectedit">Assunto:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="subjectedit" value="'.$linha['EvSubject'].'">
                        </div>
                    </div>
                    <div class="form-group" style="padding: 15px">
                        <label class="control-label col-sm-2" for="usersedit">Usuários:</label>
                        <div class="col-sm-10">
                        <select class="js-example-basic-multiple" id="usersedit" multiple="multiple" style="width:100%;display:block;box-sizing:border-box">';
                $QUERYPESSOAS = "SELECT IdUsuario, Nome FROM Usuarios WHERE Removido = 0 AND IdUsuario != ".$_COOKIE['reuniao']['IdUsuario']." ORDER BY Nome ASC";
                $resultado = mysqli_query($con, $QUERYPESSOAS);
                while($linha = mysqli_fetch_array($resultado)){
                    echo "<option value='".$linha['IdUsuario']."'>".$linha['Nome']."</option>";
                }
                echo '</select>
                        </div>
                    </div>
                </div>';
            }
        }
	    elseif($_POST['Pagina'] == "poc"){
            if($_POST['TIPO'] == "CAD"){
                $color = mysqli_query($con, "SELECT id, name, hex FROM color");
                $cores = array();
                while($linha = mysqli_fetch_array($color)){
                    $linha2 = array();
                    $linha2['id'] = $linha['id'];
                    $linha2['name'] = $linha['name'];
                    $linha2['hex'] = $linha['hex'];
                    $cores[] = $linha2;
                }
                $users = mysqli_query($con, "SELECT IdUsuario, Nome FROM Usuarios WHERE Ativo = 1 and Removido = 0 AND IdUsuario != ".$_COOKIE['reuniao']['IdUsuario']." ORDER BY Nome");
                $usuarios = array();
                while($linha = mysqli_fetch_array($users)){
                    $linha2 = array();
                    $linha2['id'] = $linha['IdUsuario'];
                    $linha2['nome'] = $linha['Nome'];
                    $usuarios[] = $linha2;
                }
                echo json_encode(['color'=> $cores, 'solicitante' => $_COOKIE['reuniao']['Nome'], 'usuarios' => $usuarios]);
            }
            elseif ($_POST['TIPO'] == "EDIT"){
                $poc = mysqli_fetch_assoc(mysqli_query($con, "SELECT poc.id, poc.title, poc.start, poc.remarks, Usuarios.Nome  FROM poc, Usuarios WHERE poc.id = ".$_POST['ID']." AND poc.user = Usuarios.IdUsuario"));
                $linhapoc = array();
                $linhapoc['id'] = $poc['id'];
                $linhapoc['title'] = $poc['title'];
                $linhapoc['color'] = $poc['color'];
                $linhapoc['start'] = $poc['start'];
                $linhapoc['remarks'] = $poc['remarks'];
                $linhapoc['user'] = $poc['Nome'];
                $users = mysqli_query($con, "SELECT IdUsuario, Nome FROM Usuarios WHERE Ativo = 1 and Removido = 0 AND IdUsuario != ".$_COOKIE['reuniao']['IdUsuario']." ORDER BY Nome");
                $usuarios = array();
                while($linha = mysqli_fetch_array($users)){
                    $linha2 = array();
                    $linha2['id'] = $linha['IdUsuario'];
                    $linha2['nome'] = $linha['Nome'];
                    $usuarios[] = $linha2;
                }
                $marcados = mysqli_query($con, "SELECT uid FROM participantes_poc WHERE pocid = ".$_POST['ID']);
                $checked = array();
                while($linha = mysqli_fetch_array($marcados)){
                    $checked[] = $linha['uid'];
                }
                echo json_encode(['poc' => $linhapoc, 'usuarios' => $usuarios, 'participantes' => $checked]);
            }
            elseif($_POST['TIPO'] == "SHOW"){
                $poc = mysqli_fetch_assoc(mysqli_query($con, "SELECT poc.id, poc.title, poc.start, poc.remarks, Usuarios.Nome  FROM poc, Usuarios WHERE poc.id = ".$_POST['ID']." AND poc.user = Usuarios.IdUsuario"));
                $linhapoc = array();
                $linhapoc['id'] = $poc['id'];
                $linhapoc['title'] = $poc['title'];
                $linhapoc['color'] = $poc['color'];
                $linhapoc['start'] = $poc['start'];
                $linhapoc['remarks'] = $poc['remarks'];
                $linhapoc['user'] = $poc['Nome'];
                $users = mysqli_query($con, "SELECT IdUsuario, Nome FROM Usuarios WHERE Ativo = 1 and Removido = 0 AND IdUsuario != ".$_COOKIE['reuniao']['IdUsuario']." ORDER BY Nome");
                $usuarios = array();
                while($linha = mysqli_fetch_array($users)){
                    $linha2 = array();
                    $linha2['id'] = $linha['IdUsuario'];
                    $linha2['nome'] = $linha['Nome'];
                    $usuarios[] = $linha2;
                }
                $marcados = mysqli_query($con, "SELECT uid FROM participantes_poc WHERE pocid = ".$_POST['ID']);
                $checked = array();
                while($linha = mysqli_fetch_array($marcados)){
                    $checked[] = $linha['uid'];
                }
                echo json_encode(['poc' => $linhapoc, 'usuarios' => $usuarios, 'participantes' => $checked]);
            }
        }
    }
	elseif($_POST['funcao'] == "ProcuraData"){
	    $QUERY1 = "SELECT * FROM `eventos` WHERE EvDate = '".$_POST['data']."' AND EvEnd >= '".$_POST['inicio']."' LIMIT 1";
	    $resultado_1 = mysqli_query($con, $QUERY1);
	    if(mysqli_num_rows($resultado_1) == 0){
            $json = '{"situacao" : "SUCESSO"}';
            echo $json;
        }
	    else{
            $linha = mysqli_fetch_assoc($resultado_1);
            if($linha['EvStart']<= $_POST['fim']){
                $json = '{"situacao" : "ERRO"}';
                echo $json;
            }
            else{
                $json = '{"situacao" : "SUCESSO"}';
                echo $json;
            }
        }
    }
	elseif($_POST['funcao'] == "CadastrarRegistro"){
	    if($_POST['TIPO'] == "NOVO"){
	        if($_POST['Pagina'] == "reunioes"){
                $sqlID = "SELECT AUTO_INCREMENT FROM   information_schema.tables WHERE  table_name = 'eventos' AND    table_schema = '".DATABASE."' ";
                $queryID = mysqli_query($con, $sqlID);
                $id = 0;
                while($row_id = mysqli_fetch_array($queryID)){
                    $id = $row_id['AUTO_INCREMENT'];
                }
	            $QUERY = "INSERT INTO eventos(EvDate, EvStart, EvEnd, EvSubject,  EvUID, EvRemovido) VALUES ('".$_POST['data']."', '".$_POST['inicio']."', '".$_POST['fim']."', '".$_POST['subject']."', ".$_COOKIE['reuniao']['IdUsuario'].", 0)";
	            mysqli_query($con, $QUERY);
	            $usuarios = $_POST['users'];
	            if(count($usuarios) >0){
                    for($i = 0; $i<count($usuarios);$i++){
                        mysqli_query($con, "INSERT IGNORE INTO admin_eventos (EvId, UId) VALUES (".$id.", ".$usuarios[$i].")");
                    }
                }
                mysqli_query($con,"INSERT INTO logs(l_log, l_datetime, l_user) VALUES ('".json_encode($_POST)."', '".date('Y-m-d H:i:s')."', ".$_COOKIE['reuniao']['IdUsuario'].")");
            }
	        elseif($_POST['Pagina'] == "poc"){
	            $sqlID = "SELECT AUTO_INCREMENT FROM   information_schema.tables WHERE  table_name = 'poc' AND    table_schema = '".DATABASE."' ";
                $queryID = mysqli_query($con, $sqlID);
                $id = 0;
                while($row_id = mysqli_fetch_array($queryID)){
                    $id = $row_id['AUTO_INCREMENT'];
                }
                mysqli_query($con, "INSERT INTO poc (title, start, remarks, user, pasta) VALUES ('".$_POST['title']."', '".$_POST['start']."','".$_POST['remarks']."', ".$_COOKIE['reuniao']['IdUsuario'].", ".$_POST['pasta'].")");
                $users = json_decode($_POST['users']);
                $pasta = mysqli_fetch_assoc(mysqli_query($con, "SELECT nomePasta FROM pasta WHERE pasta.id = ".$_POST['pasta']))['nomePasta'];
                require_once ("./inc/PHPMailer/class.phpmailer.php");
                $mail = new PHPMailer();
                if(count($users)> 0){
                    for($i = 0; $i<count($users); $i++){
                        mysqli_query($con, "INSERT INTO participantes_poc(pocid, uid) VALUES (".$id.", ".$users[$i].")");
                        $resposta = EnviarEmail($con, $users[$i], "Você acaba de ser mencionado em uma Nota no reuniao", $_POST['title'], date('d/m/Y',strtotime($_POST['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Solicitante", $pasta);
                    }
                }
                mysqli_query($con,"INSERT INTO logs(l_log, l_datetime, l_user) VALUES ('".json_encode($_POST)."', '".date('Y-m-d H:i:s')."', ".$_COOKIE['reuniao']['IdUsuario'].")");
                $resposta = EnviarEmail($con, $_COOKIE['reuniao']['IdUsuario'], "Você acaba de criar uma Nota no reuniao", $_POST['title'], date('d/m/Y',strtotime($_POST['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Solicitante", $pasta);
            }
	        elseif($_POST['Pagina'] == 'pasta'){
                mysqli_query($con, "INSERT INTO pasta (nomePasta) VALUES ('".$_POST['nomePasta']."')");
                mysqli_query($con,"INSERT INTO logs(l_log, l_datetime, l_user) VALUES ('".json_encode($_POST)."', '".date('Y-m-d H:i:s')."', ".$_COOKIE['reuniao']['IdUsuario'].")");
                echo json_encode(['resposta' => 'OK']);
            }
        }
	    elseif($_POST['TIPO'] == "EDIT"){
	        if($_POST['Pagina'] == "reunioes") {
                mysqli_query($con, "UPDATE eventos SET EvDate = '" . $_POST['data'] . "', EvSubject = '" . $_POST['subject'] . "', EvStart = '" . $_POST['inicio'] . "', EvEnd = '" . $_POST['fim'] . "' WHERE EvId = " . $_POST['ID']);
                $users = $_POST['users'];
                mysqli_query($con, 'DELETE FROM admin_eventos WHERE EvId = ' . $_POST['ID']);
                if (count($users) > 0) {
                    for ($i = 0; $i < count($users); $i++) {
                        mysqli_query($con, 'INSERT IGNORE INTO admin_eventos (EvId, UId) VALUES (' . $_POST['ID'] . ', ' . $users[$i] . ')');
                    }
                }
                mysqli_query($con,"INSERT INTO logs(l_log, l_datetime, l_user) VALUES ('".json_encode($_POST)."', '".date('Y-m-d H:i:s')."', ".$_COOKIE['reuniao']['IdUsuario'].")");
            }
            elseif($_POST['Pagina'] == "poc"){
                require_once ("./inc/PHPMailer/class.phpmailer.php");
                $mail = new PHPMailer();
                $pasta = mysqli_fetch_assoc(mysqli_query($con, "SELECT nomePasta FROM poc, pasta WHERE poc.pasta = pasta.id AND poc.id = ".$_POST['id']))['nomePasta'];

                $resposta = EnviarEmail($con, $_COOKIE['reuniao']['IdUsuario'], "Você acaba de editar uma Nota no reuniao", $_POST['title'], date('d/m/Y',strtotime($_POST['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Editor", $pasta);
                mysqli_query($con, "UPDATE poc SET title = '".$_POST['title']."', start = '".$_POST['start']."', remarks = '".$_POST['remarks']."' WHERE id = ".$_POST['id']);
                mysqli_query($con, "DELETE FROM participantes_poc WHERE pocid = ".$_POST['id']);
                $users = json_decode($_POST['users']);
                if(count($users) >0){
                    for($i = 0; $i< count($users); $i++){
                        mysqli_query($con, "INSERT INTO participantes_poc(pocid, uid) VALUES (".$_POST['id'].", ".$users[$i].")");
                        if($users[$i] != $_COOKIE['reuniao']['IdUsuario']){
                            $resposta = EnviarEmail($con, $users[$i], "Uma nota acaba de ser editada", $_POST['title'], date('d/m/Y',strtotime($_POST['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Editor", $pasta);
                        }
                    }
                }
                $SELECTNOTA = mysqli_query($con, "SELECT user FROM poc WHERE id = ".$_POST['id']);
                $usercriador = mysqli_fetch_assoc($SELECTNOTA)['user'];
                if($usercriador != $_COOKIE['reuniao']['IdUsuario']){
                    $resposta = EnviarEmail($con, $usercriador, "Sua nota acaba de ser editada", $_POST['title'], date('d/m/Y',strtotime($_POST['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Editor", $pasta);
                }
                mysqli_query($con,"INSERT INTO logs(l_log, l_datetime, l_user) VALUES ('".json_encode($_POST)."', '".date('Y-m-d H:i:s')."', ".$_COOKIE['reuniao']['IdUsuario'].")");
            }

        }
    }
	elseif($_POST['funcao'] == "SelectUsers"){
	    $resultado = mysqli_query($con, "SELECT * FROM `admin_eventos` WHERE EvId = ".$_POST['ID']);
	    $id = array();
        while($linha = mysqli_fetch_array($resultado)){
            $id[] = $linha['UId'];
        }
        echo json_encode($id);
    }
	elseif($_POST['funcao'] == "ExcluirRegistro"){
	    mysqli_query($con, "UPDATE ".$_POST['Pagina']." SET ".$_POST['CampoRemovido']." = 1 WHERE ".$_POST['CampoId']." = ".$_POST['ID']);
        mysqli_query($con,"INSERT INTO logs(l_log, l_datetime, l_user) VALUES ('".json_encode($_POST)."', '".date('Y-m-d H:i:s')."', ".$_COOKIE['reuniao']['IdUsuario'].")");
        if($_POST['Pagina'] == "poc"){
            require_once ("./inc/PHPMailer/class.phpmailer.php");
            $pasta = mysqli_fetch_assoc(mysqli_query($con, "SELECT nomePasta FROM poc, pasta WHERE poc.pasta = pasta.id AND poc.id = ".$_POST['id']))['nomePasta'];
            $dados = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM poc WHERE id = ".$_POST['ID']));
            $mail = new PHPMailer();
            $resposta = EnviarEmail($con, $_COOKIE['reuniao']['IdUsuario'], "Você acaba de excluir uma Nota no reuniao", $dados['title'], date('d/m/Y',strtotime($dados['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Editor", $pasta);
            if($_COOKIE['reuniao']['IdUsuario'] != $dados['user']){
                $resposta = EnviarEmail($con, $dados['user'], "Sua Nota Acaba de ser excluída", $dados['title'], date('d/m/Y',strtotime($dados['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Editor", $pasta);
            }
            $selectusers = mysqli_query($con, "SELECT uid FROM participantes_poc WHERE pocid = ".$_POST['ID']);
            while($linha = mysqli_fetch_array($selectusers)){
                $resposta = EnviarEmail($con, $linha['uid'], "A nota abaixo acaba de ser excluída", $dados['title'], date('d/m/Y',strtotime($dados['start'])), $_COOKIE['reuniao']['Nome'], $mail, "Editor", $pasta);
            }
        }
    }
    elseif($_POST['funcao'] == "PermissaoCadReunioes"){
	    $LINHA = mysqli_num_rows(mysqli_query($con, "SELECT * FROM Usuarios WHERE IdUsuario = ".$_COOKIE['reuniao']['IdUsuario']." AND UReuniao = 1"));
        if($LINHA > 0){
            $json = '{"situacao" : "SUCESSO"}';
            echo $json;
        }
        else{
            $json = '{"situacao" : "ERRO"}';
            echo $json;
        }
    }
	elseif($_POST['funcao'] == "EventsPOC"){
        $result_events = mysqli_query($con, "SELECT poc.id, poc.title, poc.start, Usuarios.Nome, situacao_poc.nomeSituacao FROM poc, Usuarios, situacao_poc WHERE removed = 0 AND poc.user = Usuarios.IdUsuario AND poc.pasta = ".$_POST['pasta']." AND poc.status = situacao_poc.id" );
        $events = array();
        if(mysqli_num_rows($result_events) > 0){
            echo "<table class='table display' style='font-size: 12px;' id='listar-usuario'>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Título</th>
                            <th>Data de Atualização</th>
                            <th>+</th>
                            <th>Solicitante</th>
                            <th></th>
                        </tr>                   
                    </thead>
                    <tbody>
            ";
            while($linha = mysqli_fetch_array($result_events)){
                $editar = permissaoPastas($con, 0, $linha['id']);
                $row = array();
                echo "<tr>";
                echo "<td>".$linha['id']."</td>";
                echo "<td>".$linha['title']."</td>";
                echo "<td>".date('d/m/Y', strtotime($linha['start']))."</td>";
                echo "<td>".$linha['Nome']."</td>";
                echo "<td>".$linha['nomeSituacao']."</td>";
                echo "<td>";
                echo "<a class='btn btn-success glyphicon glyphicon-search btn-xs' href='#' onclick='BuscaCadastro({$linha['id']}, \"SHOW\")' title='Visualizar'></a>";
                if($editar){
                    echo "&nbsp;<a class='btn btn-warning btn-xs glyphicon glyphicon-pencil' onclick='BuscaCadastro({$linha['id']}, \"EDIT\")' title='Editar'></a>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
        else{
            echo "<h5>Não há Notas cadastradas</h5>";
        }
    }
	elseif ($_POST['funcao'] == "ListarPastas"){
	    $SELECTPASTAS = mysqli_query($con, "SELECT id, nomePasta FROM pasta ORDER BY nomePasta ASC");
	    $pastas = array();
	    while ($linha = mysqli_fetch_array($SELECTPASTAS)){
            $linha2 = array();
            $linha2['id'] = $linha['id'];
            $linha2['nome'] = $linha['nomePasta'];
            $pastas[] = $linha2;
        }
        echo json_encode(['pastas' => $pastas]);
    }
} else {
	echo 'ERRO';
}
?>