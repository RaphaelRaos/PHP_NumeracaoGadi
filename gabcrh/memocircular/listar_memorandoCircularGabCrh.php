<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './MemorandoCircularGabCrh.php';

    
    $listar = new MemorandoCircularGabCrh();
    $listar->listarMemorandoCircular();    
        
?>