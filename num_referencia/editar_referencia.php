<?php

//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: *");

require './NumeroReferencia.php';

$response_json = file_get_contents("php://input");
$dados = json_decode($response_json,true);

if($dados){

    $editReferencia = new NumeroReferencia();
    $editReferencia->editarReferencia($dados);

   $response = [
       "erro" => false,
       "dados" => $dados,
       "mensagem" =>"Número de Referencia Editado!"
   ];
} else {

    $response = [
        "erro" => true,        
        "mensagem" =>"Número de Referencia não editado! Favor Validar as informações (Error -> 02B) "
    ];
}

http_response_code (200);
echo json_encode($response);