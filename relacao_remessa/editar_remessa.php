<?php

//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: *");

require './RelacaoRemessa.php';

$response_json = file_get_contents("php://input");
$dados = json_decode($response_json,true);

if($dados){

    $editOficio = new RelacaoRemessa();
    $editOficio->editarRemessa($dados);

   $response = [
       "erro" => false,
       "dados" => $dados,
       "mensagem" =>"Relação de Remessa Editada!"
   ];
} else {

    $response = [
        "erro" => true,        
        "mensagem" =>"Relação de Remessa não editada! Favor Validar as informações (Error -> 02B) "
    ];
}

http_response_code (200);
echo json_encode($response);