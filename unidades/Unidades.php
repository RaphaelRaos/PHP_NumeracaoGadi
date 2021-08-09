<?php

include_once "../conexao/Conexao.php";

class Unidades extends Conexao{

    protected $connection;
    protected $dados;

    public function visualizarUA() {
        $conn = new Conexao(); 
        $this->connection = $conn->conectar();


        $query_list_ua = "SELECT CodTabUa, [Cod UA] AS COD_UA, [Des UA] AS DES_UA FROM [Consulta_Unidades] ORDER BY [Des UA]";
        $result_list_ua = $this->connection->prepare($query_list_ua);
        $result_list_ua->execute();
        
        if(($result_list_ua)AND ($result_list_ua->rowCount()!=0)){
            while($resultado_UA = $result_list_ua->fetch(PDO::FETCH_ASSOC)){
                extract($resultado_UA);
                $lista_UA["registro_UA"][$CodTabUa] = [
                    "CodTabUa" => $CodTabUa,
                    "CODIGO_UA" => $COD_UA,
                    "UNIDADE_ADMINISTRATIVA" => $DES_UA
                ];
            }
            //RESPOSTA COM STATUS 200;
            http_response_code(200);
            //RETORNAR OS PROTUDOS EM FORMATO JSON
            echo json_encode($lista_UA);
        }
    }

    public function visualizarUO(){
        $conn = new Conexao();
        $this->connection = $conn->conectar();

        $query_list_uo = "SELECT CodTabUGO, [Cod UGO] as Cod_UGO, [Des UGO] as DesUGO FROM [Consulta_Unidades] ORDER BY [Cod UGO]";
        $result_list_uo = $this->connection->prepare($query_list_uo);
        $result_list_uo->execute();

        if(($result_list_uo)AND ($result_list_uo->rowCount()!=0)){
            while ($resultado_UO = $result_list_uo->fetch(PDO::FETCH_ASSOC)){
                extract($resultado_UO);
                $lista_UO['registro_UO'][$CodTabUGO] = [
                    "CodTabUGO" => $CodTabUGO,
                    "Cod_UGO" => $Cod_UGO,
                    'UNIDADE_ORCAMENTARIA' => $DesUGO
                ];
            }
            http_response_code(200);
            echo json_encode($lista_UO);
        }

    }


}