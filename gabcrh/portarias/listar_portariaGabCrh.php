<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './PortariaGabCrh.php';

    
    $listar = new PortariaGabCrh();
    $listar->listarPortaria();    
        
?>