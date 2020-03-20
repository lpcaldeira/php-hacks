<?php

header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '"*"'));
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
set_time_limit(0); // remove limite de tempo de execução

require_once $_SERVER['DOCUMENT_ROOT'] . "/configs/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/configs/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/configs/util.php";

// Busca todas as tables do banco de dados
$arrayTabelas = array();
$query = "show tables";
$result = getData($query);

while($row = mysqli_fetch_array($result)){
    // Se for um campo string e não for a chave e se a tabela não existe ainda
    if (strpos($row['Tables_in_myempire'], 'leonardo') === false){
        if (strpos($row['Tables_in_myempire'], '_BACKUP_UTF8') === false){
            array_push($arrayTabelas, $row['Tables_in_myempire']);
        } 
        else {
            // Remove da lista as tabelas pelas quais ele já atualizou o valor
            $tabelaOriginal = str_replace('_BACKUP_UTF8', '', $row['Tables_in_myempire']);
            $key = array_search($tabelaOriginal, $arrayTabelas);
            unset($arrayTabelas[$key]);
        }
    }
}

// Cria uma tabela de backup antes de mexer na original
foreach($arrayTabelas as $tabela) {

    // Se já existe, só vai retornar erro e seguir o processo
    $createBackup = "CREATE TABLE ".$tabela."_BACKUP_UTF8 LIKE $tabela; ";
    $createBackup .= "INSERT ".$tabela."_BACKUP_UTF8 SELECT * FROM $tabela; ";

    execMultiCommand($createBackup);

    // Busca todas as colunas da tabela que sejam string e não possuam _id
    $arrayColunas = array();
    $query = "describe $tabela";
    $result = getData($query);

    while($row = mysqli_fetch_array($result)){
        // Se for um campo string e não for a chave
        if (stripos($row['Type'], 'varchar') === 0 || stripos($row['Type'], 'text') === 0){
            if (stripos($row['Field'], 'id_') !== 0){
                array_push($arrayColunas, $row['Field']);
            }
        }
    }

    $update = "UPDATE $tabela SET ";

    $firstTime = 1;
    foreach($arrayColunas as $coluna) {
        $columnEncoded = utf8_encode($coluna);
        if (!$firstTime == 1){
            $update .= ",";
        }
        $firstTime = 0;
        $update .= $columnEncoded." = convert(cast(convert($columnEncoded using latin1) as binary) using utf8)";
    }
    $update .= "WHERE 1";

    execMultiCommand($update);

}

?>