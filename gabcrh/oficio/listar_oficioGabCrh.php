<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './OficioGabCrh.php';

    
    $listar = new OficioGabCrh();
    $listar->listarOficio();    
        
?>