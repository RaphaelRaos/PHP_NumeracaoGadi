<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './MemorandoGabCrh.php';

    
    $listar = new MemorandoGabCrh();
    $listar->listarMemorando();    
        
?>