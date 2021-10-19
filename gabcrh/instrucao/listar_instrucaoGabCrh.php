<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './InstrucaoGabCrh.php';

    
    $listar = new InstrucaoGabCrh();
    $listar->listarInstrucao();    
        
?>