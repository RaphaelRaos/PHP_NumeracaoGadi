<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './RemessaGabCrh.php';

    
    $listar = new RemessaGabCrh();
    $listar->listarRemessa();    
        
?>