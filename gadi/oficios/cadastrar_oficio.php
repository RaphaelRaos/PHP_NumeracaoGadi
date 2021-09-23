<?php
//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header ("Content-Type: application/json; charset=UTF-8");
header ("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: *");

require './Oficios.php';

$response_json = file_get_contents("php://input");
$dados = json_decode($response_json,true);

    if ($dados){
        
        $cadOficios = new Oficios();
        $cadOficios->cadastrarOficio($dados);

        $response = [
            "erro" => false,
            "mensagem" => "Número de Referência Cadastrado com Sucesso"
        ];
    } else {
        $response = [
            "erro" => true,
            "mensagem" => "Número de Referência não Cadastrado !! (Erro 2-B)"
        ];
    }
    
    http_response_code(200);
    echo json_encode($response);

?>