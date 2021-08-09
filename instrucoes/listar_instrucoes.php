<?php

//CABEÇALHOS OBRIGATÓRIOS


    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: *");
   

    require './Instrucoes.php';

    $listar = new Instrucoes();
    $listar->listarInstrucoes();