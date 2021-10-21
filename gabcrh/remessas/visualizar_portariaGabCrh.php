<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require './RemessaGabCrh.php';
 
    
 $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
 $visualizar = new RemessaGabCrh();
 $visualizar->visualizarRemessa($id);

?>