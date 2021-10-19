<?php

//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
header("Access-Control-Allow-Headers: *");

require './InformacaoGabCrh.php';


$response_json = file_get_contents("php://input");
$dados = json_decode($response_json, true);
$cadInformacao;

if ($dados) {

    $cadInformacao = new InformacaoGabCrh();
    $cadInformacao->cadastrarInformacao($dados);
   
} else {

    $response = [
        "erro" => true,
        "mensagem" => "Informacao não cadasTRado!"
    ];
    echo json_encode($response);
}

http_response_code(200);
