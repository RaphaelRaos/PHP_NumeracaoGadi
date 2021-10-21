<?php
//CABEÇALHOS OBRIGATÓRIOS

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
    header("Access-Control-Allow-Headers: *");

    require './OficioCircularGabCrh.php';
    

    $response_json = file_get_contents("php://input");
    $dados = json_decode($response_json,true);

    if($dados) {

        $excluirOficioCircular = new OficioCircularGabCrh();
        $excluirOficioCircular->excluirOficioCircular($dados);
       
        $response = [
            "erro" => false,
            "mensagem" => "Oficio Circular Excluído!"
        ];
    } else {
        $response = [
            "erro" => true,
            "mensagem" => "Oficio Circular não excluído! (Erro 2-B)"
        ];
    }
    http_response_code(200);
    echo json_encode($response);