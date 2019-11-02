<?php

require_once './inc/db_connect.php';
mysqli_set_charset ($con, 'utf8');
//Receber a requisão da pesquisa
$requestData= $_REQUEST;


//nome da coluna no banco de dados
$columns = array(
    0 => 'number',
    1 => 'givenname',
    2 => 'surname',
    3 => 'city',
    4 => 'streetaddress'
);
//Consulta base sem filtros (caso campo de pesquisa vazio)
$resultUsers = "SELECT number, givenname,surname, city, streetaddress FROM fakenames WHERE nameset = 'Brazil'";


$resultadoUsers =mysqli_query($con, $resultUsers);
$qnt_linhas = mysqli_num_rows($resultadoUsers);

if(!empty($requestData['search']['value']) ) {   // se houver um parâmetro de pesquisa, $requestData['search']['value'] contém o parâmetro de pesquisa
    $resultUsers.=" AND ( number LIKE '%".$requestData['search']['value']."%' ";
    $resultUsers.=" OR givenname LIKE '%".$requestData['search']['value']."%' ";
    $resultUsers.=" OR surname LIKE '%".$requestData['search']['value']."%' ";
    $resultUsers.=" OR city LIKE '%".$requestData['search']['value']."%' ";
    $resultUsers.=" OR streetaddress LIKE '%".$requestData['search']['value']."%' )";

//echo $resultUsers;
    $resultadoUsers=mysqli_query($con, $resultUsers);
}
//echo $resultUsers;

$totalFiltered = mysqli_num_rows($resultadoUsers);
//Ordenar o resultado
$resultUsers.=" ORDER BY  ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
$resultadoUsers = mysqli_query($con, $resultUsers);
// Ler e criar o array de dados
$dados = array();
while($row_users = mysqli_fetch_array($resultadoUsers) ) {
    $dado = array();
    $dado[] = $row_users["number"];
    $dado[] = $row_users["givenname"];
    $dado[] = $row_users["surname"];
    $dado[] = $row_users["city"];
    $dado[] = $row_users["streetaddress"];
    $dados[] = $dado;
}
$json_data = array(
    "draw" => intval( $requestData['draw'] ),//para cada requisição é enviado um número como parâmetro
    "recordsTotal" => intval( $qnt_linhas ),  //Quantidade de registros que há no banco de dados
    "recordsFiltered" => intval( $totalFiltered ), //Total de registros quando houver pesquisa
    "data" => $dados   //Array de dados completo dos dados retornados da tabela
);
echo json_encode($json_data);  //enviar dados como formato json
?>