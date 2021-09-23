<?php
//CABEÇALHOS OBRIGATÓRIOS

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
    header("Access-Control-Allow-Headers: *");

   
    require './Assuntos.php';

        $Assunto = new Assuntos();
        $Assunto->visualizarAssunto();

        $response_json = file_get_contents("php://input");
        $dados = json_decode($response_json,true);