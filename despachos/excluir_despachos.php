<?php
//CABEÇALHOS OBRIGATÓRIOS

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
    header("Access-Control-Allow-Headers: *");

    require './Despachos.php';
    

    $response_json = file_get_contents("php://input");
    $dados = json_decode($response_json,true);

    if($dados) {

        $excluirDespacho = new Despachos();
        $excluirDespacho->excluirDespacho($dados);
       
        $response = [
            "erro" => false,
            "mensagem" => "Despacho Excluído!"
        ];
    } else {
        $response = [
            "erro" => true,
            "mensagem" => "Despacho não excluído! (Erro 2-B)"
        ];
    }
    http_response_code(200);
    echo json_encode($response);