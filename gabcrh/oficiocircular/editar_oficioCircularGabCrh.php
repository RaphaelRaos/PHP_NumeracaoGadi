<?php

//CABEÇALHOS OBRIGATÓRIOS


    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: *");
   
    
    require './OficioCircularGabCrh.php';
    
    $response_json = file_get_contents("php://input"); 
    $dados = json_decode($response_json,true);

    if ($dados){
            
            $editOficioCircular = new OficioCircularGabCrh();
            $editOficioCircular->editarOficioCircular($dados);

       $response = [
           "error" => false,
           "dados" => $dados,
           "mensagem" => "Oficio Circular Editado"
       ];
       
} else {

    $response = [
        "error" => true,
        "mensagem" => "Oficio Circular não editado. Favor Validar as informações (Error -> 02B)"
        
    ];
}

http_response_code (200);
echo json_encode($response);