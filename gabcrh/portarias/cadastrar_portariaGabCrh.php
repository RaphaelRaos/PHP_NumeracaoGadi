<?php

//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
header("Access-Control-Allow-Headers: *");

require './PortariaGabCrh.php';


$response_json = file_get_contents("php://input");
$dados = json_decode($response_json, true);
$cadPortaria;

if ($dados) {

    $cadPortaria = new PortariaGabCrh();
    $cadPortaria->cadastrarPortaria($dados);
   
} else {

    $response = [
        "erro" => true,
        "mensagem" => "Portaria não cadasTRada!"
    ];
    echo json_encode($response);
}

http_response_code(200);
