<?php
//define("HOST", "localhost"); // O host no qual vocÍ deseja se conectar.
define("HOST", "127.0.0.1"); // O host no qual vocÍ deseja se conectar.
define("USER", "root"); // O nome de usuário do banco de dados.
define("PASSWORD", ""); // A senha do usuário do banco de dados.
define("DATABASE", "datatables"); // O nome do banco de dados.
 
$con = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
// Se voce estiver se conectando via TCP/IP ao invés de um socket UNIX, lembre-se de adicionar o número da porta como um parametro.

if (mysqli_connect_errno()){
  echo "Não foi possivel se conectar ao servidor SQL: " . mysqli_connect_error();
}
?>
