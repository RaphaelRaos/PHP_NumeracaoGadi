<?php
//CABEÇALHOS OBRIGATÓRIOS

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods:GET,PUT,POST, DELETE");
    header("Access-Control-Allow-Headers: *");

    require './InstrucaoGabCrh.php';
    

    $response_json = file_get_contents("php://input");
    $dados = json_decode($response_json,true);

    if($dados) {

        $excluirInstrucao = new InstrucaoGabCrh();
        $excluirInstrucao->excluirInstrucao($dados);
       
        $response = [
            "erro" => false,
            "mensagem" => "Instrucao Excluída!"
        ];
    } else {
        $response = [
            "erro" => true,
            "mensagem" => "Instrucao não excluída! (Erro 2-B)"
        ];
    }
    http_response_code(200);
    echo json_encode($response);