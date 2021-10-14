<?php

//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
header("Access-Control-Allow-Headers: *");

require './ComunicadosGabCrh.php';


$response_json = file_get_contents("php://input");
$dados = json_decode($response_json, true);
$cadComunicado;

if ($dados) {

    $cadComunicado = new ComunicadosGabCrh();
    $cadComunicado->cadastrarComunicados($dados);

    
} else {

    $response = [
        "erro" => true,
        "mensagem" => "Comunicado não cadasTRado!"
    ];
    echo json_encode($response);
}

http_response_code(200);
