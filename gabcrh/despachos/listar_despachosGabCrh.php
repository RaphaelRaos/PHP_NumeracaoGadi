<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './DespachosGabCrh.php';

    
    $listar = new DespachosGabCrh();
    $listar->listarDespachos();    
        
?>