<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './SetoresGabCrh.php';

    
    $listar = new SetoresGabCrh();
    $listar->listarDepartamentos();    
        
?>