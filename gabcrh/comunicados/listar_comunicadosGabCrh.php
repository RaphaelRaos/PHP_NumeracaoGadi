<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './ComunicadosGabCrh.php';

    
    $listar = new ComunicadosGabCrh();
    $listar->listarComunicados();    
        
?>