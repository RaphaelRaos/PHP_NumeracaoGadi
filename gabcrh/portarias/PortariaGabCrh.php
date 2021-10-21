<?php

include_once '../../conexao/Conexao.php';

class PortariaGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarPortaria($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_portaria = "INSERT INTO numeracaoGabCoordenadorCrhPortarias 
        (datElaboracao_portaria, automaticoCriacao_portaria, anoCriacao_portaria, assunto_portaria, executor_portaria, setorElaboracao_portaria, exclusao_portaria, observacao_portaria)
        VALUES
        (:datElaboracao_portaria, GETDATE(), YEAR(GETDATE()), :assunto_portaria, :executor_portaria, :setorElaboracao_portaria, 0, :observacao_portaria) ";

        $cad_portaria = $this->connect->prepare($query_portaria);

        $cad_portaria->bindParam(':datElaboracao_portaria', $dados['portaria']['datElaboracao_portaria']);
        $cad_portaria->bindParam(':assunto_portaria', $dados['portaria']['assunto_portaria']);
        $cad_portaria->bindParam(':executor_portaria', $dados['portaria']['executor_portaria']);
        $cad_portaria->bindParam(':setorElaboracao_portaria', $dados['portaria']['setorElaboracao_portaria']);
        $cad_portaria->bindParam(':observacao_portaria', $dados['portaria']['observacao_portaria']);
        $cad_portaria->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_portaria, numero_portaria
        FROM numeracaoGabCoordenadorCrhPortarias
        WHERE executor_portaria = :executor_portaria
        ORDER BY id_portaria DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_portaria', $dados['portaria']['executor_portaria']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_portaria =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_portaria);
            $portaria = [
                "erro" => false,
                "mensagem" => "Portaria cadastrada!",
                'id_portaria' => $id_portaria,
                'numero_portaria' => $numero_portaria
            ];
            http_response_code(200);
            echo json_encode($portaria);
        }
    }

    public function listarPortaria()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_portaria_list = "SELECT 
        id_portaria, numero_portaria, datElaboracao_portaria, assunto_portaria, executor_portaria, setorElaboracao_portaria, exclusao_portaria, observacao_portaria,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoportaria, numeracaoDepartamento.nome_departamento as setorportaria
        FROM numeracaoGabCoordenadorCrhPortarias
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhPortarias.assunto_portaria = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhPortarias.setorElaboracao_portaria = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_portaria = 0 
        ORDER BY id_portaria DESC
        ";

        $result_list_portaria = $this->connect->prepare($query_portaria_list);
        $result_list_portaria->execute();

        if (($result_list_portaria) and ($result_list_portaria->rowCount() != 0)) {
            while ($result_portaria = $result_list_portaria->fetch(PDO::FETCH_ASSOC)) {
                extract($result_portaria);
                $lista_portaria[$id_portaria] = [
                    'id_portaria' => $id_portaria,
                    'numero_portaria' => $numero_portaria,
                    'assunto_portaria' => $assunto_portaria,
                    'assuntoportaria' => $assuntoportaria,
                    'datElaboracao_portaria' => $datElaboracao_portaria,
                    'executor_portaria' => $executor_portaria,
                    'setorElaboracao_portaria' => $setorElaboracao_portaria,
                    'setorportaria' => $setorportaria,
                    'observacao_portaria' => $observacao_portaria
                ];
            }
            http_response_code(200);
            echo json_encode($lista_portaria);
        }
    }

    public function visualizarPortaria($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_portaria = "SELECT
        id_portaria, numero_portaria, datElaboracao_portaria, assunto_portaria, executor_portaria, setorElaboracao_portaria, observacao_portaria,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoportaria, numeracaoDepartamento.nome_departamento as setorportaria
        FROM numeracaoGabCoordenadorCrhPortarias
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhPortarias.assunto_portaria = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhPortarias.setorElaboracao_portaria = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_portaria = 0 and id_portaria = :id";

        $result_portaria = $this->connect->prepare($query_visualizar_portaria);
        $result_portaria->bindParam(':id', $id, PDO::PARAM_INT);
        $result_portaria->execute();

        if (($result_portaria) and ($result_portaria->rowCount() != 0)) {
            $row_portaria = $result_portaria->fetch(PDO::FETCH_ASSOC);
            extract($row_portaria);
            $portaria = [
                'id_portaria' => $id_portaria,
                'numero_portaria' => $numero_portaria,
                'assunto_portaria' => $assunto_portaria,
                'assuntoportaria' => $assuntoportaria,
                'datElaboracao_portaria' => $datElaboracao_portaria,
                'executor_portaria' => $executor_portaria,
                'setorElaboracao_portaria' => $setorElaboracao_portaria,
                'setorportaria' => $setorportaria,
                'observacao_portaria' => $observacao_portaria
            ];

            $response = [
                "erro" => false,
                "portaria" => $portaria
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Portaria não encontrada!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarPortaria($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarportaria = "UPDATE numeracaoGabCoordenadorCrhPortarias
        SET assunto_portaria = :assunto_portaria , executor_portaria = :executor_portaria, setorElaboracao_portaria = :setorElaboracao_portaria, 
        observacao_portaria = :observacao_portaria 
        WHERE id_portaria = :id_portaria ";

        $editportaria = $this->connect->prepare($query_editarportaria);
        $editportaria->bindParam(':assunto_portaria', $dados['assunto_portaria'], PDO::PARAM_INT);
        $editportaria->bindParam(':executor_portaria', $dados['executor_portaria'], PDO::PARAM_STR);
        $editportaria->bindParam(':setorElaboracao_portaria', $dados['setorElaboracao_portaria'], PDO::PARAM_INT);
        $editportaria->bindParam(':observacao_portaria', $dados['observacao_portaria'], PDO::PARAM_STR);
        $editportaria->bindParam(':id_portaria', $dados['id_portaria'], PDO::PARAM_INT);

        $editportaria->execute();
        
    }

    public function newListarPortaria($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_portaria = $this->listarPortaria();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_portaria, numero_portaria, datElaboracao_portaria, assunto_portaria, executor_portaria, setorElaboracao_portaria, observacao_portaria,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoportaria, numeracaoDepartamento.nome_departamento as setorportaria
            FROM numeracaoGabCoordenadorCrhPortarias
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhPortarias.assunto_portaria = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhPortarias.setorElaboracao_portaria = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_portaria = 0  and numero_portaria like :numero_portaria OR
            exclusao_portaria = 0  and executor_portaria like :executor_portaria OR
            exclusao_portaria = 0  and assunto_portaria like :assunto_portaria OR  
            exclusao_portaria = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntoportaria OR
            exclusao_portaria = 0  and numeracaoDepartamento.nome_departamento LIKE :setorportaria
            ORDER BY id_portaria DESC";

            $listar_portaria = $this->connect->prepare($newListar);
            $listar_portaria->bindParam(':numero_portaria', $ParLike, PDO::PARAM_INT);
            $listar_portaria->bindParam(':executor_portaria', $ParLike, PDO::PARAM_STR);
            $listar_portaria->bindParam(':assunto_portaria', $ParLike, PDO::PARAM_INT);
            $listar_portaria->bindParam(':assuntoportaria', $ParLike, PDO::PARAM_STR);
            $listar_portaria->bindParam(':setorportaria', $ParLike, PDO::PARAM_STR);
            $listar_portaria->execute();

            if (($listar_portaria) and ($listar_portaria->rowCount() != 0)) {
                while ($result_portaria = $listar_portaria->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_portaria);
                    $lista_portaria[$id_portaria] = [
                        'id_portaria' => $id_portaria,
                        'numero_portaria' => $numero_portaria,
                        'assunto_portaria' => $assunto_portaria,
                        'assuntoportaria' => $assuntoportaria,
                        'datElaboracao_portaria' => $datElaboracao_portaria,
                        'executor_portaria' => $executor_portaria,
                        'setorElaboracao_portaria' => $setorElaboracao_portaria,
                        'setorportaria' => $setorportaria,
                        'observacao_portaria' => $observacao_portaria
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_portaria);
            }
        }
    }

    public function excluirPortaria($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_portaria_list = "UPDATE numeracaoGabCoordenadorCrhPortarias
        SET automaticoExclusao_portaria = GETDATE(), exclusao_portaria = 1 
        WHERE id_portaria= :id";

        $exclusaoportaria = $this->connect->prepare($query_portaria_list);
        $exclusaoportaria->bindParam(':id', $dados['id_portaria']);
        $exclusaoportaria->execute();
    }
}