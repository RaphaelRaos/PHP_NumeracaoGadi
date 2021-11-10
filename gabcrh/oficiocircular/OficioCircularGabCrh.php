<?php

include_once '../../conexao/Conexao.php';

class OficioCircularGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarOficioCircular($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_oficioCircular = "INSERT INTO numeracaoGabCoordenadorCrhOficioCircular 
        (datElaboracao_oficioCircular, automaticoCriacao_oficioCircular, anoCriacao_oficioCircular, assunto_oficioCircular, executor_oficioCircular, setorElaboracao_oficioCircular, exclusao_oficioCircular, observacao_oficioCircular)
        VALUES
        (:datElaboracao_oficioCircular, GETDATE(), YEAR(GETDATE()), :assunto_oficioCircular, :executor_oficioCircular, :setorElaboracao_oficioCircular, 0, :observacao_oficioCircular) ";

        $cad_oficioCircular = $this->connect->prepare($query_oficioCircular);

        $cad_oficioCircular->bindParam(':datElaboracao_oficioCircular', $dados['oficioCircular']['datElaboracao_oficioCircular']);
        $cad_oficioCircular->bindParam(':assunto_oficioCircular', $dados['oficioCircular']['assunto_oficioCircular']);
        $cad_oficioCircular->bindParam(':executor_oficioCircular', $dados['oficioCircular']['executor_oficioCircular']);
        $cad_oficioCircular->bindParam(':setorElaboracao_oficioCircular', $dados['oficioCircular']['setorElaboracao_oficioCircular']);
        $cad_oficioCircular->bindParam(':observacao_oficioCircular', $dados['oficioCircular']['observacao_oficioCircular']);
        $cad_oficioCircular->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_oficioCircular, numero_oficioCircular
        FROM numeracaoGabCoordenadorCrhOficioCircular
        WHERE executor_oficioCircular = :executor_oficioCircular
        ORDER BY id_oficioCircular DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_oficioCircular', $dados['oficioCircular']['executor_oficioCircular']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_oficioCircular =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_oficioCircular);
            $oficioCircular = [
                "erro" => false,
                "mensagem" => "oficioCircular cadastrado!",
                'id_oficioCircular' => $id_oficioCircular,
                'numero_oficioCircular' => $numero_oficioCircular
            ];
            http_response_code(200);
            echo json_encode($oficioCircular);
        }
    }

    public function listarOficioCircular()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_oficioCircular_list = "SELECT 
        id_oficioCircular, numero_oficioCircular, datElaboracao_oficioCircular, assunto_oficioCircular, executor_oficioCircular, setorElaboracao_oficioCircular, exclusao_oficioCircular, observacao_oficioCircular,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntooficioCircular, numeracaoDepartamento.nome_departamento as setoroficioCircular
        FROM numeracaoGabCoordenadorCrhOficioCircular
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhOficioCircular.assunto_oficioCircular = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhOficioCircular.setorElaboracao_oficioCircular = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_oficioCircular = 0 
        ORDER BY id_oficioCircular DESC
        ";

        $result_list_oficioCircular = $this->connect->prepare($query_oficioCircular_list);
        $result_list_oficioCircular->execute();

        if (($result_list_oficioCircular) and ($result_list_oficioCircular->rowCount() != 0)) {
            while ($result_oficioCircular = $result_list_oficioCircular->fetch(PDO::FETCH_ASSOC)) {
                extract($result_oficioCircular);
                $lista_oficioCircular[$id_oficioCircular] = [
                    'id_oficioCircular' => $id_oficioCircular,
                    'numero_oficioCircular' => $numero_oficioCircular,
                    'assunto_oficioCircular' => $assunto_oficioCircular,
                    'assuntooficioCircular' => $assuntooficioCircular,
                    'datElaboracao_oficioCircular' => date('d/m/Y', strtotime($datElaboracao_oficioCircular)),
                    'executor_oficioCircular' => $executor_oficioCircular,
                    'setorElaboracao_oficioCircular' => $setorElaboracao_oficioCircular,
                    'setoroficioCircular' => $setoroficioCircular,
                    'observacao_oficioCircular' => $observacao_oficioCircular
                ];
            }
            http_response_code(200);
            echo json_encode($lista_oficioCircular);
        }
    }

    public function visualizarOficioCircular($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_oficioCircular = "SELECT
        id_oficioCircular, numero_oficioCircular, datElaboracao_oficioCircular, assunto_oficioCircular, executor_oficioCircular, setorElaboracao_oficioCircular, observacao_oficioCircular,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntooficioCircular, numeracaoDepartamento.nome_departamento as setoroficioCircular
        FROM numeracaoGabCoordenadorCrhOficioCircular
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhOficioCircular.assunto_oficioCircular = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhOficioCircular.setorElaboracao_oficioCircular = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_oficioCircular = 0 and id_oficioCircular = :id";

        $result_oficioCircular = $this->connect->prepare($query_visualizar_oficioCircular);
        $result_oficioCircular->bindParam(':id', $id, PDO::PARAM_INT);
        $result_oficioCircular->execute();

        if (($result_oficioCircular) and ($result_oficioCircular->rowCount() != 0)) {
            $row_oficioCircular = $result_oficioCircular->fetch(PDO::FETCH_ASSOC);
            extract($row_oficioCircular);
            $oficioCircular = [
                'id_oficioCircular' => $id_oficioCircular,
                'numero_oficioCircular' => $numero_oficioCircular,
                'assunto_oficioCircular' => $assunto_oficioCircular,
                'assuntooficioCircular' => $assuntooficioCircular,
                'datElaboracao_oficioCircular' => date('d/m/Y', strtotime($datElaboracao_oficioCircular)),
                'executor_oficioCircular' => $executor_oficioCircular,
                'setorElaboracao_oficioCircular' => $setorElaboracao_oficioCircular,
                'setoroficioCircular' => $setoroficioCircular,
                'observacao_oficioCircular' => $observacao_oficioCircular
            ];

            $response = [
                "erro" => false,
                "oficioCircular" => $oficioCircular
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "oficioCircular não encontrado!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarOficioCircular($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editaroficioCircular = "UPDATE numeracaoGabCoordenadorCrhOficioCircular
        SET assunto_oficioCircular = :assunto_oficioCircular , executor_oficioCircular = :executor_oficioCircular, setorElaboracao_oficioCircular = :setorElaboracao_oficioCircular, 
        observacao_oficioCircular = :observacao_oficioCircular 
        WHERE id_oficioCircular = :id_oficioCircular ";

        $editoficioCircular = $this->connect->prepare($query_editaroficioCircular);
        $editoficioCircular->bindParam(':assunto_oficioCircular', $dados['assunto_oficioCircular'], PDO::PARAM_INT);
        $editoficioCircular->bindParam(':executor_oficioCircular', $dados['executor_oficioCircular'], PDO::PARAM_STR);
        $editoficioCircular->bindParam(':setorElaboracao_oficioCircular', $dados['setorElaboracao_oficioCircular'], PDO::PARAM_INT);
        $editoficioCircular->bindParam(':observacao_oficioCircular', $dados['observacao_oficioCircular'], PDO::PARAM_STR);
        $editoficioCircular->bindParam(':id_oficioCircular', $dados['id_oficioCircular'], PDO::PARAM_INT);

        $editoficioCircular->execute();
        
    }

    public function newListarOficioCircular($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_oficioCircular = $this->listarOficioCircular();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_oficioCircular, numero_oficioCircular, datElaboracao_oficioCircular, assunto_oficioCircular, executor_oficioCircular, setorElaboracao_oficioCircular, observacao_oficioCircular,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntooficioCircular, numeracaoDepartamento.nome_departamento as setoroficioCircular
            FROM numeracaoGabCoordenadorCrhOficioCircular
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhOficioCircular.assunto_oficioCircular = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhOficioCircular.setorElaboracao_oficioCircular = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_oficioCircular = 0  and numero_oficioCircular like :numero_oficioCircular OR
            exclusao_oficioCircular = 0  and executor_oficioCircular like :executor_oficioCircular OR
            exclusao_oficioCircular = 0  and assunto_oficioCircular like :assunto_oficioCircular OR  
            exclusao_oficioCircular = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntooficioCircular OR
            exclusao_oficioCircular = 0  and numeracaoDepartamento.nome_departamento LIKE :setoroficioCircular
            ORDER BY id_oficioCircular DESC";

            $listar_oficioCircular = $this->connect->prepare($newListar);
            $listar_oficioCircular->bindParam(':numero_oficioCircular', $ParLike, PDO::PARAM_INT);
            $listar_oficioCircular->bindParam(':executor_oficioCircular', $ParLike, PDO::PARAM_STR);
            $listar_oficioCircular->bindParam(':assunto_oficioCircular', $ParLike, PDO::PARAM_INT);
            $listar_oficioCircular->bindParam(':assuntooficioCircular', $ParLike, PDO::PARAM_STR);
            $listar_oficioCircular->bindParam(':setoroficioCircular', $ParLike, PDO::PARAM_STR);
            $listar_oficioCircular->execute();

            if (($listar_oficioCircular) and ($listar_oficioCircular->rowCount() != 0)) {
                while ($result_oficioCircular = $listar_oficioCircular->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_oficioCircular);
                    $lista_oficioCircular[$id_oficioCircular] = [
                        'id_oficioCircular' => $id_oficioCircular,
                        'numero_oficioCircular' => $numero_oficioCircular,
                        'assunto_oficioCircular' => $assunto_oficioCircular,
                        'assuntooficioCircular' => $assuntooficioCircular,
                        'datElaboracao_oficioCircular' => date('d/m/Y', strtotime($datElaboracao_oficioCircular)),
                        'executor_oficioCircular' => $executor_oficioCircular,
                        'setorElaboracao_oficioCircular' => $setorElaboracao_oficioCircular,
                        'setoroficioCircular' => $setoroficioCircular,
                        'observacao_oficioCircular' => $observacao_oficioCircular
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_oficioCircular);
            }
        }
    }

    public function excluirOficioCircular($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_oficioCircular_list = "UPDATE numeracaoGabCoordenadorCrhOficioCircular
        SET automaticoExclusao_oficioCircular = GETDATE(), exclusao_oficioCircular = 1 
        WHERE id_oficioCircular= :id";

        $exclusaooficioCircular = $this->connect->prepare($query_oficioCircular_list);
        $exclusaooficioCircular->bindParam(':id', $dados['id_oficioCircular']);
        $exclusaooficioCircular->execute();
    }
}