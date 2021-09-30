<?php

include_once '../../conexao/Conexao.php';

class Andamentos extends Conexao {

    protected $connect;
    protected $dados;

    public function visualizarStatus(){

        $conn = new Conexao();
        $this->connection = $conn->conectar();

        $query_list_status = "SELECT id_andamento, status_andamento 
        FROM numeracaoGadiAndamentoProcessos";
        $result_list_status = $this->connection->prepare($query_list_status);
        $result_list_status->execute();

        if(($result_list_status) AND ($result_list_status->rowCount() !=0)){
            while ($result_status = $result_list_status->fetch(PDO::FETCH_ASSOC)){
                extract($result_status);
                $lista_status['lista_status'][$id_andamento] = [
                    'id_andamento' => $id_andamento,
                    'status_andamento' => $status_andamento
                ];
            }
            http_response_code(200);
            echo json_encode($lista_status);
        }
    }
}