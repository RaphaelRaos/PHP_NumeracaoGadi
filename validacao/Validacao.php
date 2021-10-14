<?php


class Validacao
{   
    
    public function validacaoComunicado($dados)
    {
        if ($dados['comunicado']["datEmissao_comunicado"] > date("Y/m/d") || $dados['comunicado']["datEmissao_comunicado"]  == null) {
           return "Dados Invalidos";
                      
        } else if ($dados['comunicado']["assunto_comunicado"]  == null) {
            $response = [
                "error" => true,
                "mensagem" => "Comunicado não Cadastrado. Validar informações no campo Assunto.(Error -> 02B)"

            ];
            echo json_encode($response);
        } else if ($dados['comunicado']["executor_comunicado"]  == null) {
            $response = [
                "error" => true,
                "mensagem" => "Comunicado não Cadastrado. Validar informações no campo Executor.(Error -> 02B)"

            ];
            echo json_encode($response);
        } else if ($dados['comunicado']["setorElaboracao_comunicado"]  == null) {
            $response = [
                "error" => true,
                "mensagem" => "Comunicado não Cadastrado. Validar informações no campo Setor.(Error -> 02B)"

            ];
            echo json_encode($response);
        } else {

            
        }
    }
}
