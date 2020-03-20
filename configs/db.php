<?php
require_once('config.php');

function getConnection(){
    $cnx = mysqli_connect(DB_HOST, DB_LOGIN, DB_PWD) or die('Ocorreu um erro na conexão com o banco de dados.' . mysql_error());
    mysqli_select_db($cnx, DB_NAME) or die('Ocorreu um erro na conexão com o banco de dados.' . mysql_error());
    mysqli_set_charset($cnx, 'latin1');
    return $cnx;
}

function getData($query, $cnx = null){
    if(!isset($cnx)){
        $cnx = getConnection(); 
    }
    $result = mysqli_query($cnx, $query);
    mysqli_close($cnx);
    return $result;
}

function execMultiCommand($query, $cnx = null){
	if(!isset($cnx)){
		$cnx = getConnection();
	}
	$result = mysqli_multi_query($cnx, $query);
	mysqli_close($cnx);
	return $result;
}

?>