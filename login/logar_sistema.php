<?php
//CABEÇALHOS OBRIGATÓRIOS

header("Access-Control-Allow-Origin: *");
header ("Content-Type: application/json; charset=UTF-8");
header ("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: *");

require './Login.php';

$response_json = file_get_contents("php://input");
$dados = json_decode($response_json,true);

if ($dados){
        
    $login = new login();
    $login->loginUsuario($dados);

    $response = [
        "erro" => false,
        "mensagem" => "Login"
    ];
} else {
    $response = [
        "erro" => true,
        "mensagem" => "Não Login"
    ];
}