<?php

//CABEÇALHOS OBRIGATÓRIOS


    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: *");
   
    
    require './MemorandoCircularGabCrh.php';
    
    $response_json = file_get_contents("php://input"); 
    $dados = json_decode($response_json,true);

    if ($dados){
            
            $editMemorandoCircular = new MemorandoCircularGabCrh();
            $editMemorandoCircular->editarMemorandoCircular($dados);

       $response = [
           "error" => false,
           "dados" => $dados,
           "mensagem" => "Memorando Circular Editado"
       ];
       
} else {

    $response = [
        "error" => true,
        "mensagem" => "Memorando Circular não editado. Favor Validar as informações (Error -> 02B)"
        
    ];
}

http_response_code (200);
echo json_encode($response);