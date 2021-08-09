<?php

include_once '../conexao/Conexao.php';

class Assuntos extends Conexao {

    protected $connect;
    protected $dados;

    public function visualizarAssunto(){
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_list_assunto = "SELECT id_assunto, assunto FROM tb_assuntos";
        $result_list_assunto = $this->connect->prepare($query_list_assunto);
        $result_list_assunto->execute();

        if(($result_list_assunto)AND ($result_list_assunto->rowCount() !=0)){
            while($resultadoAssunto = $result_list_assunto->fetch(PDO::FETCH_ASSOC)){
                extract($resultadoAssunto);
                $listAssunto['registro_assunto'][$id_assunto] = [
                    'id_assunto' => $id_assunto,
                    'assunto' => $assunto
                ];
            }
        }
        http_response_code(200);
        echo json_encode($listAssunto);
    }
}