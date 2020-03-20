<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once $_SERVER['DOCUMENT_ROOT'].'/database.php';
include_once $GLOBALS['fisical_objects_path'].'/schedule.php';
include_once $GLOBALS['fisical_libs_path'].'/php_jwt/JWT.php';

use Dompdf\Exception;
use \Firebase\JWT\JWT;

$database = new Database();
$db = $database->getConnection();

$schedule = new Schedule($db);

$data = json_decode(file_get_contents("php://input"));

$jwt=isset($data->jwt) ? $data->jwt : "";
if ($jwt){

    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
    }
    catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "result" => false,
            "message" => "Acesso negado. " . $e->getMessage()
        ));
        die();
    }

    if(!isset($data->inicio)){
        $errorMessage = "Informe uma data início para buscar os agendamentos.";
    }
    else if (!isset($data->fim)){
        $errorMessage = "Informe uma data fim para buscar os agendamentos.";
    }
    
    if (empty($errorMessage)){

        try{
            $schedule->dia = $data->inicio;
            $schedule->jwt = $jwt;
            $search_available_time = json_decode($schedule->search_available_time())->records;
            $num_available = count($search_available_time);
        }
        catch(Exception $err){
            $num_available = 0;
        }

        $schedule->inicio = $data->inicio;
        $schedule->fim = $data->fim;
        $ler = $schedule->read();

        if ($ler->rowCount() > 0){
            $schedules_arr=array();
            $schedules_arr["records"]=array();
            $schedules_arr["result"]=true;
            
            while ($row = $ler->fetch(PDO::FETCH_ASSOC)){
                extract($row);
        
                $schedule_item=array(
                    "id" => $id,
                    "inicio" => $inicio,
                    "fim" => $fim
                );
                
                array_push($schedules_arr["records"], $schedule_item);
            }

            if ($num_available > 0){
                $schedules_arr["records"] = array_merge($search_available_time, $schedules_arr["records"]);
                $dataInicio = array_column($schedules_arr["records"], 'inicio');
                array_multisort($dataInicio, SORT_ASC, $schedules_arr["records"]);
            }
        }
        
        http_response_code(200);
        echo json_encode($schedules_arr,JSON_PRETTY_PRINT);
    }
    else{
        http_response_code(401);
        echo json_encode(array("result" => false, "message" => $errorMessage));
    }
}
else{
    http_response_code(401);
    echo json_encode(array(
        "result" => false,
        "message" => "Acesso negado."
    ));
}

/*
*********************
EXEMPLO DE REQUISIÇÃO 
{
	"inicio": "2019-11-05 00:00",
    "fim": "2019-11-05 23:59",
    "jwt": "f97g9f59g5g5da9fgd87g7.s87dg87dfg876df76gd87fg.87sd87dg876df876g7df-7d6f87gd6f876d7"
}

*******************
EXEMPLO DE RESPOSTA
{
    "records": [
        {
            "inicio": "2019-11-05 08:00:00",
            "fim": "2019-11-05 08:30:00"
        },
        {
            "inicio": "2019-11-05 08:30:00",
            "fim": "2019-11-05 09:00:00"
        },
        {
            "inicio": "2019-11-05 09:00:00",
            "fim": "2019-11-05 09:30:00"
        },
        {
            "inicio": "2019-11-05 09:30:00",
            "fim": "2019-11-05 10:00:00"
        },
        {
            "inicio": "2019-11-05 10:00:00",
            "fim": "2019-11-05 10:30:00"
        },
        {
            "inicio": "2019-11-05 10:30:00",
            "fim": "2019-11-05 11:00:00"
        },
        {
            "inicio": "2019-11-05 11:00:00",
            "fim": "2019-11-05 11:30:00"
        },
        {
            "inicio": "2019-11-05 11:30:00",
            "fim": "2019-11-05 12:00:00"
        },
        {
            "inicio": "2019-11-05 13:30:00",
            "fim": "2019-11-05 14:00:00"
        },
        {
            "inicio": "2019-11-05 14:00:00",
            "fim": "2019-11-05 14:30:00"
        },
        {
            "inicio": "2019-11-05 14:30:00",
            "fim": "2019-11-05 15:00:00"
        },
        {
            "inicio": "2019-11-05 15:00:00",
            "fim": "2019-11-05 15:30:00"
        },
        {
            "inicio": "2019-11-05 15:30:00",
            "fim": "2019-11-05 16:00:00"
        },
        {
            "inicio": "2019-11-05 16:00:00",
            "fim": "2019-11-05 16:30:00"
        },
        {
            "inicio": "2019-11-05 16:30:00",
            "fim": "2019-11-05 17:00:00"
        },
        {
            "inicio": "2019-11-05 17:00:00",
            "fim": "2019-11-05 17:30:00"
        },
        {
            "inicio": "2019-11-05 17:30:00",
            "fim": "2019-11-05 18:00:00"
        },
        {
            "inicio": "2019-11-05 18:00:00",
            "fim": "2019-11-05 18:30:00"
        },
        {
            "inicio": "2019-11-05 18:30:00",
            "fim": "2019-11-05 19:00:00"
        },
        {
            "inicio": "2019-11-05 19:00:00",
            "fim": "2019-11-05 19:30:00"
        },
        {
            "inicio": "2019-11-05 19:30:00",
            "fim": "2019-11-05 20:00:00"
        },
        {
            "inicio": "2019-11-05 20:00:00",
            "fim": "2019-11-05 20:30:00"
        },
        {
            "id": "123456",
            "inicio": "2019-11-05 20:12:00",
            "fim": "2019-11-05 21:30:00",
        },
        {
            "id": "123457",
            "inicio": "2019-11-05 21:30:00",
            "fim": "2019-11-05 22:00:00",
        },
        {
            "id": "123458",
            "inicio": "2019-11-05 22:00:00",
            "fim": "2019-11-05 22:10:00",
        }
    ],
    "result": true
}
*/
