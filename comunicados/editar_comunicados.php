<?php

//CABEÇALHOS OBRIGATÓRIOS


    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: *");
   
    
    require './Comunicados.php';
    
    $response_json = file_get_contents("php://input"); 
    $dados = json_decode($response_json,true);

    if ($dados){
            
            $editComunicado = new Comunicados();
            $editComunicado->editarComunicado($dados);

        $response = [
            "erro" => false,
            "dados" => $dados,
            "mensagem" => "Comunicado editado!"            
        ];
} else {

    $response = [
        "error" => true,
        "mensagem" => "Comunicado não editado. Favor Validar as informações (Error -> 02B)"
        
    ];
}

http_response_code (200);
echo json_encode($response);