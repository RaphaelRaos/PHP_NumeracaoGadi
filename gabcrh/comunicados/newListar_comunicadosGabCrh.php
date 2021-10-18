<?php

//CABEÇALHOS OBRIGATÓRIOS


    header("Access-Control-Allow-Origin: *");
    header ("Content-Type: application/json; charset=UTF-8");
    header ("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
    header ("Access-Control-Allow-Headers: *");
   
    $response_json = file_get_contents("php://input");
    $dados = json_decode($response_json,true);

    require './ComunicadosGabCrh.php';
    

    if ($dados == null){
        $BuscaFinal = null;
    } else {
        foreach($dados as $Busca){
            $BuscaFinal=$Busca;
        }
    }

    $listar = new ComunicadosGabCrh();
    $listar->newListarComunicados($BuscaFinal);