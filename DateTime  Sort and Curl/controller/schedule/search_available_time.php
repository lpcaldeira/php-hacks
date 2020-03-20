<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once $GLOBALS['fisical_libs_path'].'/php_jwt/JWT.php';
use \Firebase\JWT\JWT;

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

    // Converte em data e hora
    if (isset($data->dia)){
        $hora_inicio_agenda_aux = new DateTime($data->dia);
    }

    // Cria listas de retorno
    $schedules_arr=array();
    $schedules_arr["records"]=array();
    $schedules_arr["result"]=true;
    
    // Tempo entre cada horário
    $tempo_agendamento_aux = 30;

    // Se o usuário enviou uma data como Entrada, utilizar ela
    if (isset($hora_inicio_agenda_aux)){
        $hora_inicio_agenda_aux = new DateTime($hora_inicio_agenda_aux->format("Y-m-d $hora_inicio_agenda"));
        $hora_fim_agenda_aux = new DateTime($hora_inicio_agenda_aux->format("Y-m-d $hora_fim_agenda"));
    }
    else{
        $hora_inicio_agenda_aux = new DateTime('2019-11-05 08:00:00');
        $hora_fim_agenda_aux = new DateTime('2019-11-05 20:30:00');
    }

    // Retira período do meio dia
    $hora_inicio_meio_dia = new DateTime($hora_inicio_agenda_aux->format("Y-m-d 12:00:00"));
    $hora_fim_meio_dia = new DateTime($hora_inicio_meio_dia->format("Y-m-d H:i:s"));
    // Adiciona 1 hora e 30 minutos
    $hora_fim_meio_dia = $hora_fim_meio_dia->add(new DateInterval('PT90M'));

    // Último horário disponível
    $hora_fim_agenda_aux2 = new DateTime($hora_inicio_agenda_aux->format('Y-m-d H:i:s'));

    // Enquanto o período não chegou ao fim, continua gerando horários
    while($hora_inicio_agenda_aux <= $hora_fim_agenda_aux){
        $hora_fim_agenda_aux2->add(new DateInterval('PT' . $tempo_agendamento_aux . 'M'));

        if ($hora_inicio_agenda_aux >= $hora_inicio_meio_dia &&
            $hora_inicio_agenda_aux < $hora_fim_meio_dia){
            $hora_inicio_agenda_aux = new DateTime($hora_fim_agenda_aux2->format('Y-m-d H:i:s'));
            continue;
        }

        $record_item=array(
            "inicio" => $hora_inicio_agenda_aux->format('Y-m-d H:i:s'),
            "fim" => $hora_fim_agenda_aux2->format('Y-m-d H:i:s'),
        );
        $hora_inicio_agenda_aux = new DateTime($hora_fim_agenda_aux2->format('Y-m-d H:i:s'));
        array_push($schedules_arr['records'], $record_item);
    }

    http_response_code(200);
    echo json_encode($schedules_arr,JSON_PRETTY_PRINT);
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
	"dia": "2019-11-05",
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
        }
    ],
    "result": true
}
*/
