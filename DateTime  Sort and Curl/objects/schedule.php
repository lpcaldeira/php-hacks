<?php

Class Schedule{

    private $conn;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    // Verifica disponibilidade de horários
    function search_available_time(){

        if (strpos($_SERVER["SERVER_NAME"], "localhost") !== false){
            $url = "http://";
        }
        else $url = "https://";

        $data = array(
            "dia" => $this->dia,
            "jwt" => $this->jwt
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.$_SERVER["SERVER_NAME"]."/controllers/schedule/search_available_time.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'accept: application/vnd.api+json',
                'cache-control: no-cache',
                'content-type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;
    }
}
