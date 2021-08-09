<?php
//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './NumeroReferencia.php';
 
    
 $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
 
 $visualizar = new NumeroReferencia();
 $visualizar->visualizarReferencia($id);