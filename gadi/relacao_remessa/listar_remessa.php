<?php

//CABEÇALHOS OBRIGATÓRIOS


    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: *");
   

    require './RelacaoRemessa.php';

    $listar = new RelacaoRemessa();
    $listar->listarRemessa();