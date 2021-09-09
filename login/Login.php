<?php

include_once '../conexao/Conexao.php';

class Login extends Conexao
{
    protected object $connect;
    protected $dados;

    public function loginUsuario($dados)
    {

        if ($dados) {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $queryLogin = "SELECT  rede_usuario, senha_usuario FROM tb_usuarios 
           WHERE rede_usuario = :rede_usuario AND senha_usuario = :senha_usuario";

            $result_login = $this->connect->prepare($queryLogin);
            $result_login->bindParam(':rede_usuario', $dados['login']['rede_usuario'], PDO::PARAM_STR);
            $result_login->bindParam(':senha_usuario', $dados['login']['senha_usuario'], PDO::PARAM_STR);
            $result_login->execute();

            if (($result_login) and ($result_login->rowCount() != 0)) {

                $queryAcesso = "SELECT rede_usuario,
                tb_departamentos.id_departamento as id_departamento,
                nome_departamento                  
                FROM tb_usuarioDepartamento
                INNER JOIN tb_usuarios on tb_usuarioDepartamento.id_usuario = tb_usuarios.id_usuario
                INNER JOIN tb_departamentos on tb_usuarioDepartamento.id_departamento = tb_departamentos.id_departamento
                WHERE rede_usuario = :rede_usuario";

                $result_acesso = $this->connect->prepare($queryAcesso);
                $result_acesso->bindParam(':rede_usuario', $dados['login']['rede_usuario'], PDO::PARAM_STR);
                $result_acesso->execute();

                if (($result_acesso) and ($result_acesso->rowCount() != 0)) {
                    while ($list_Setor = $result_acesso->fetch(PDO::FETCH_ASSOC)) {
                        extract($list_Setor);
                        $setor[] = [
                            'rede_usuario' => $rede_usuario,
                            'id_departamento' => $id_departamento,

                        ];
                    }
                    //RESPOSTA COM STATUS 200;
                    http_response_code(200);
                    //RETORNAR OS PROTUDOS EM FORMATO JSON
                    echo json_encode($setor);
                } else {
                    $response = [
                        "erro" => true,
                        "mensagem" => "USUÁRIO SEM SETOR CADASTRADO."
                    ]; 
                }
            } else {
                $response = [
                    "erro" => true,
                    "mensagem" => "USUÁRIO OU SENHA INVÁLIDA, FAVOR VALIDAR AS INFORMAÇÕES."
                ];
                http_response_code(200);
                echo json_encode($response);
            }            
        }
    }
}
