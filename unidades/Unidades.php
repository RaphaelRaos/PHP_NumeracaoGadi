<?php

include_once "../conexao/Conexao.php";

class Unidades extends Conexao{

    protected $connection;
    protected $dados;

    public function visualizarUnidades() {
        $conn = new Conexao(); 
        $this->connection = $conn->conectar();


        $query_list_unidades = "SELECT 
        [Tabela UA].CodTabUa, [Tabela UA].[Cod UA] AS CODIGO_UA,[Tabela UA].[Des UA] AS DESCRICAO_UA, 
        [Tabela UGE].CodTabUGE, [Tabela UGE].[Cod UGE] AS CODIGO_UGE, [Tabela UGE].[Des UGE] AS DESCRICAO_UGE,
        [Tabela UGO].CodTabUGO, [Tabela UGO].[Cod UGO] AS CODIGO_UGO, [Tabela UGO].[Des UGO] AS DESCRICAO_UGO        
        FROM
        [Tabela UA] 

        INNER JOIN [Tabela UGE] ON [Tabela UA].CodTabUGE = [Tabela UGE].CodTabUGE
        INNER JOIN [TABELA UGO] ON [Tabela UGE].CodTabUGO = [Tabela UGO].CodTabUGO
        WHERE [Tabela UA].[Cod Status Unidade] = 9 AND [Tabela UA].NomenclaturaStatus = 1
        ORDER BY [Tabela UA].[Des UA]";


        $result_list_unidades = $this->connection->prepare($query_list_unidades);
        $result_list_unidades->execute();
        
        if(($result_list_unidades)AND ($result_list_unidades->rowCount()!=0)){
            while($resultado_unidades = $result_list_unidades->fetch(PDO::FETCH_ASSOC)){
                extract($resultado_unidades);
                $lista_unidades["registro_unidades"][$CodTabUa] = [
                    "CodTabUa" => $CodTabUa,
                    "CODIGO_UA" => $CODIGO_UA,
                    "DESCRICAO_UA" => $DESCRICAO_UA,
                    "CodTabUGE" => $CodTabUGE,
                    "CODIGO_UGE" => $CODIGO_UGE,
                    "DESCRICAO_UGE" => $DESCRICAO_UGE,
                    "CodTabUGO" => $CodTabUGO,
                    "CODIGO_UGO" => $CODIGO_UGO,
                    "DESCRICAO_UGO" => $DESCRICAO_UGO
                ];
            }
            //RESPOSTA COM STATUS 200;
            http_response_code(200);
            //RETORNAR OS PROTUDOS EM FORMATO JSON
            echo json_encode($lista_unidades);
        }
    }
    
}