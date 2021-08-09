<?php

include_once '../conexao/Conexao.php';

class Login extends Conexao {
    protected object $connect;
    protected $dados;

    public function loginUsuario($dados) {

        if ($dados){

            $conn = new Conexao();
            $this->connect = $conn->conectar();

           $queryLogin = "SELECT nome_usuario, senha_usuario, setor_usuario FROM tb_usuarios 
           WHERE nome_usuario = :nome_usuario AND senha_usuario = :senha_usuario";

            $result_login= $this->connect->prepare($queryLogin);
            $result_login->bindParam(':nome_usuario',$dados['login']['nome_usuario'], PDO::PARAM_STR);
            $result_login->bindParam(':senha_usuario',$dados['login']['senha_usuario'], PDO::PARAM_STR);
            $result_login->execute();
            
            if (($result_login)AND ($result_login->rowCount() !=0)){

                 $response = [
                    "erro" => false,
                    "mensagem" => "Login realizado "
                ];
            }else {
                 $response = [
                    "erro" => true,
                    "mensagem" => "USUÁRIO OU SENHA INVÁLIDA, FAVOR VALIDAR AS INFORMAÇÕES."
                ];
            }
            http_response_code(200);
            echo json_encode($response);       
    }   

}
}