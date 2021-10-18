<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './ComunicadosGabCrh.php';
 
    
 $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
 $visualizar = new ComunicadosGabCrh();
 $visualizar->visualizarComunicados($id);

?>