<?php

include_once '../../conexao/Conexao.php';

class SetoresGabCrh extends Conexao
{
    protected $connect;
    

    public function listarDepartamentos()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_Departamento_list = "SELECT 
        id_deparatamento, nome_departamento 
        FROM 
        numeracaoDepartamento";

        $result_list_departamento = $this->connect->prepare($query_Departamento_list);
        $result_list_departamento->execute();

        if (($result_list_departamento) and ($result_list_departamento->rowCount() != 0)) {
            while ($result_departamento = $result_list_departamento->fetch(PDO::FETCH_ASSOC)) {
                extract($result_departamento);
                $lista_departamento['registro_departamento'][$id_deparatamento] = [
                    'id_deparatamento' => $id_deparatamento,
                    'nome_departamento' => $nome_departamento                    
                ];
            }
            http_response_code(200);
            echo json_encode($lista_departamento);
        }
    }
}