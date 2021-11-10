<?php

include_once '../../conexao/Conexao.php';

class MemorandoCircularGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarMemorandoCircular($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_memorandoCircular = "INSERT INTO numeracaoGabCoordenadorCrhMemorandoCircular 
        (datElaboracao_memorandoCircular, automaticoCriacao_memorandoCircular, anoCriacao_memorandoCircular, assunto_memorandoCircular, executor_memorandoCircular, setorElaboracao_memorandoCircular, exclusao_memorandoCircular, observacao_memorandoCircular)
        VALUES
        (:datElaboracao_memorandoCircular, GETDATE(), YEAR(GETDATE()), :assunto_memorandoCircular, :executor_memorandoCircular, :setorElaboracao_memorandoCircular, 0, :observacao_memorandoCircular) ";

        $cad_memorandoCircular = $this->connect->prepare($query_memorandoCircular);

        $cad_memorandoCircular->bindParam(':datElaboracao_memorandoCircular', $dados['memorandoCircular']['datElaboracao_memorandoCircular']);
        $cad_memorandoCircular->bindParam(':assunto_memorandoCircular', $dados['memorandoCircular']['assunto_memorandoCircular']);
        $cad_memorandoCircular->bindParam(':executor_memorandoCircular', $dados['memorandoCircular']['executor_memorandoCircular']);
        $cad_memorandoCircular->bindParam(':setorElaboracao_memorandoCircular', $dados['memorandoCircular']['setorElaboracao_memorandoCircular']);
        $cad_memorandoCircular->bindParam(':observacao_memorandoCircular', $dados['memorandoCircular']['observacao_memorandoCircular']);
        $cad_memorandoCircular->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_memorandoCircular, numero_memorandoCircular
        FROM numeracaoGabCoordenadorCrhMemorandoCircular
        WHERE executor_memorandoCircular = :executor_memorandoCircular
        ORDER BY id_memorandoCircular DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_memorandoCircular', $dados['memorandoCircular']['executor_memorandoCircular']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_memorandoCircular =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_memorandoCircular);
            $memorandoCircular = [
                "erro" => false,
                "mensagem" => "memorando Circular cadastrado!",
                'id_memorandoCircular' => $id_memorandoCircular,
                'numero_memorandoCircular' => $numero_memorandoCircular
            ];
            http_response_code(200);
            echo json_encode($memorandoCircular);
        }
    }

    public function listarMemorandoCircular()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_memorandoCircular_list = "SELECT 
        id_memorandoCircular, numero_memorandoCircular, datElaboracao_memorandoCircular, assunto_memorandoCircular, executor_memorandoCircular, setorElaboracao_memorandoCircular, exclusao_memorandoCircular, observacao_memorandoCircular,
        numeracaoDepartamento.nome_departamento as setormemorandoCircular
        FROM numeracaoGabCoordenadorCrhMemorandoCircular
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhMemorandoCircular.setorElaboracao_memorandoCircular = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_memorandoCircular = 0 
        ORDER BY id_memorandoCircular DESC
        ";

        $result_list_memorandoCircular = $this->connect->prepare($query_memorandoCircular_list);
        $result_list_memorandoCircular->execute();

        if (($result_list_memorandoCircular) and ($result_list_memorandoCircular->rowCount() != 0)) {
            while ($result_memorandoCircular = $result_list_memorandoCircular->fetch(PDO::FETCH_ASSOC)) {
                extract($result_memorandoCircular);
                $lista_memorandoCircular[$id_memorandoCircular] = [
                    'id_memorandoCircular' => $id_memorandoCircular,
                    'numero_memorandoCircular' => $numero_memorandoCircular,
                    'assunto_memorandoCircular' => $assunto_memorandoCircular,
                    'datElaboracao_memorandoCircular' => date('d/m/Y',strtotime($datElaboracao_memorandoCircular)),
                    'executor_memorandoCircular' => $executor_memorandoCircular,
                    'setorElaboracao_memorandoCircular' => $setorElaboracao_memorandoCircular,
                    'setormemorandoCircular' => $setormemorandoCircular,
                    'observacao_memorandoCircular' => $observacao_memorandoCircular
                ];
            }
            http_response_code(200);
            echo json_encode($lista_memorandoCircular);
        }
    }

    public function visualizarMemorandoCircular($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_memorandoCircular = "SELECT
        id_memorandoCircular, numero_memorandoCircular, datElaboracao_memorandoCircular, assunto_memorandoCircular, executor_memorandoCircular, setorElaboracao_memorandoCircular, observacao_memorandoCircular,
        numeracaoDepartamento.nome_departamento as setormemorandoCircular
        FROM numeracaoGabCoordenadorCrhMemorandoCircular
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhMemorandoCircular.setorElaboracao_memorandoCircular = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_memorandoCircular = 0 and id_memorandoCircular = :id";

        $result_memorandoCircular = $this->connect->prepare($query_visualizar_memorandoCircular);
        $result_memorandoCircular->bindParam(':id', $id, PDO::PARAM_INT);
        $result_memorandoCircular->execute();

        if (($result_memorandoCircular) and ($result_memorandoCircular->rowCount() != 0)) {
            $row_memorandoCircular = $result_memorandoCircular->fetch(PDO::FETCH_ASSOC);
            extract($row_memorandoCircular);
            $memorandoCircular = [
                'id_memorandoCircular' => $id_memorandoCircular,
                'numero_memorandoCircular' => $numero_memorandoCircular,
                'assunto_memorandoCircular' => $assunto_memorandoCircular,
                'datElaboracao_memorandoCircular' => date('d/m/Y',strtotime($datElaboracao_memorandoCircular)),
                'executor_memorandoCircular' => $executor_memorandoCircular,
                'setorElaboracao_memorandoCircular' => $setorElaboracao_memorandoCircular,
                'setormemorandoCircular' => $setormemorandoCircular,
                'observacao_memorandoCircular' => $observacao_memorandoCircular
            ];

            $response = [
                "erro" => false,
                "memorandoCircular" => $memorandoCircular
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "memorandoCircular não encontrada!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarMemorandoCircular($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarMemorandoCircular = "UPDATE numeracaoGabCoordenadorCrhMemorandoCircular
        SET assunto_memorandoCircular = :assunto_memorandoCircular , executor_memorandoCircular = :executor_memorandoCircular, setorElaboracao_memorandoCircular = :setorElaboracao_memorandoCircular, 
        observacao_memorandoCircular = :observacao_memorandoCircular 
        WHERE id_memorandoCircular = :id_memorandoCircular ";

        $editmemorandoCircular = $this->connect->prepare($query_editarMemorandoCircular);
        $editmemorandoCircular->bindParam(':assunto_memorandoCircular', $dados['assunto_memorandoCircular'], PDO::PARAM_INT);
        $editmemorandoCircular->bindParam(':executor_memorandoCircular', $dados['executor_memorandoCircular'], PDO::PARAM_STR);
        $editmemorandoCircular->bindParam(':setorElaboracao_memorandoCircular', $dados['setorElaboracao_memorandoCircular'], PDO::PARAM_INT);
        $editmemorandoCircular->bindParam(':observacao_memorandoCircular', $dados['observacao_memorandoCircular'], PDO::PARAM_STR);
        $editmemorandoCircular->bindParam(':id_memorandoCircular', $dados['id_memorandoCircular'], PDO::PARAM_INT);

        $editmemorandoCircular->execute();
        
    }

    public function newListarMemorandoCircular($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_memorandoCircular = $this->listarMemorandoCircular();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_memorandoCircular, numero_memorandoCircular, datElaboracao_memorandoCircular, assunto_memorandoCircular, executor_memorandoCircular, setorElaboracao_memorandoCircular, observacao_memorandoCircular,
            numeracaoDepartamento.nome_departamento as setormemorandoCircular
            FROM numeracaoGabCoordenadorCrhMemorandoCircular
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhMemorandoCircular.setorElaboracao_memorandoCircular = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_memorandoCircular = 0  and numero_memorandoCircular like :numero_memorandoCircular OR
            exclusao_memorandoCircular = 0  and executor_memorandoCircular like :executor_memorandoCircular OR
            exclusao_memorandoCircular = 0  and assunto_memorandoCircular like :assunto_memorandoCircular OR  
            exclusao_memorandoCircular = 0  and numeracaoDepartamento.nome_departamento LIKE :setormemorandoCircular
            ORDER BY id_memorandoCircular DESC";

            $listar_memorandoCircular = $this->connect->prepare($newListar);
            $listar_memorandoCircular->bindParam(':numero_memorandoCircular', $ParLike, PDO::PARAM_INT);
            $listar_memorandoCircular->bindParam(':executor_memorandoCircular', $ParLike, PDO::PARAM_STR);
            $listar_memorandoCircular->bindParam(':assunto_memorandoCircular', $ParLike, PDO::PARAM_STR);
            $listar_memorandoCircular->bindParam(':setormemorandoCircular', $ParLike, PDO::PARAM_STR);
            $listar_memorandoCircular->execute();

            if (($listar_memorandoCircular) and ($listar_memorandoCircular->rowCount() != 0)) {
                while ($result_memorandoCircular = $listar_memorandoCircular->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_memorandoCircular);
                    $lista_memorandoCircular[$id_memorandoCircular] = [
                        'id_memorandoCircular' => $id_memorandoCircular,
                        'numero_memorandoCircular' => $numero_memorandoCircular,
                        'assunto_memorandoCircular' => $assunto_memorandoCircular,
                        'datElaboracao_memorandoCircular' => date('d/m/Y',strtotime($datElaboracao_memorandoCircular)),
                        'executor_memorandoCircular' => $executor_memorandoCircular,
                        'setorElaboracao_memorandoCircular' => $setorElaboracao_memorandoCircular,
                        'setormemorandoCircular' => $setormemorandoCircular,
                        'observacao_memorandoCircular' => $observacao_memorandoCircular
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_memorandoCircular);
            }
        }
    }

    public function excluirMemorandoCircular($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_memorandoCircular_list = "UPDATE numeracaoGabCoordenadorCrhMemorandoCircular
        SET automaticoExclusao_memorandoCircular = GETDATE(), exclusao_memorandoCircular = 1 
        WHERE id_memorandoCircular= :id";

        $exclusaomemorandoCircular = $this->connect->prepare($query_memorandoCircular_list);
        $exclusaomemorandoCircular->bindParam(':id', $dados['id_memorandoCircular']);
        $exclusaomemorandoCircular->execute();
    }
}
