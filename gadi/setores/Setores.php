<?php

include_once '../../conexao/Conexao.php';

class Setores extends Conexao{
    protected $connect;
    protected $dados;

    public function visualizarSetor(){

        $conn = new Conexao();
        $this->connection = $conn->conectar();

        $query_list_setor = "SELECT id_setor, nome_setor FROM numeracaoSetor WHERE local_ativo = 1 AND id_departamento = 1 ";
        $result_list_setor = $this->connection->prepare($query_list_setor);
        $result_list_setor->execute();

        if(($result_list_setor) AND ($result_list_setor->rowCount() !=0)){
            while($result_setor = $result_list_setor->fetch(PDO::FETCH_ASSOC)){
                extract($result_setor);
                $lista_setor['registro_setor'][$id_setor] = [
                    'id_setor' => $id_setor,
                    'nome_setor' => $nome_setor,
                    
                ];
            }
            http_response_code(200);
            echo json_encode($lista_setor);
        }

    }
}