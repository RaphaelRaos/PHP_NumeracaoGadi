<?php

//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './Comunicados.php';

    
    $listar = new Comunicados();
    $listar->listar();    
        
?>