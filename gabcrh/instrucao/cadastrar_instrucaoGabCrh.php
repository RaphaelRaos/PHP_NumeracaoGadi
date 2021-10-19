<?php

//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
header("Access-Control-Allow-Headers: *");

require './InstrucaoGabCrh.php';


$response_json = file_get_contents("php://input");
$dados = json_decode($response_json, true);
$cadInstrucao;

if ($dados) {

    $cadInstrucao = new InstrucaoGabCrh();
    $cadInstrucao->cadastrarInstrucao($dados);
   
} else {

    $response = [
        "erro" => true,
        "mensagem" => "Instruçao não cadasTRada!"
    ];
    echo json_encode($response);
}

http_response_code(200);