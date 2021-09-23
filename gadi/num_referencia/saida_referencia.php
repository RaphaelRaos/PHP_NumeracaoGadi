<?php
//CABEÇALHOS OBRIGATÓRIOS

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
    header("Access-Control-Allow-Headers: *");

    require './NumeroReferencia.php';
    

    $response_json = file_get_contents("php://input");
    $dados = json_decode($response_json,true);

    if($dados) {

        $saidaReferencia = new NumeroReferencia();
        $saidaReferencia->saidaReferencia($dados);
       
        $response = [
            "erro" => false,
            "mensagem" => "Saída Realizada com sucesso!"
        ];
    } else {
        $response = [
            "erro" => true,
            "mensagem" => "Saída não realizada! (Erro 2-B)"
        ];
    }
    http_response_code(200);
    echo json_encode($response);