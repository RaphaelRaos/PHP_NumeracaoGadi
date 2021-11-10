<?php

include_once '../../conexao/Conexao.php';

class InformacaoGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarInformacao($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_informacao = "INSERT INTO numeracaoGabCoordenadorCrhInformacoes 
        (datElaboracao_informacao, automaticoCriacao_informacao, anoCriacao_informacao, assunto_informacao, executor_informacao, setorElaboracao_informacao, exclusao_informacao, observacao_informacao)
        VALUES
        (:datElaboracao_informacao, GETDATE(), YEAR(GETDATE()), :assunto_informacao, :executor_informacao, :setorElaboracao_informacao, 0, :observacao_informacao) ";

        $cad_informacao = $this->connect->prepare($query_informacao);

        $cad_informacao->bindParam(':datElaboracao_informacao', $dados['informacao']['datElaboracao_informacao']);
        $cad_informacao->bindParam(':assunto_informacao', $dados['informacao']['assunto_informacao']);
        $cad_informacao->bindParam(':executor_informacao', $dados['informacao']['executor_informacao']);
        $cad_informacao->bindParam(':setorElaboracao_informacao', $dados['informacao']['setorElaboracao_informacao']);
        $cad_informacao->bindParam(':observacao_informacao', $dados['informacao']['observacao_informacao']);
        $cad_informacao->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_informacao, numero_informacao
        FROM numeracaoGabCoordenadorCrhInformacoes
        WHERE executor_informacao = :executor_informacao
        ORDER BY id_informacao DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_informacao', $dados['informacao']['executor_informacao']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_informacao =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_informacao);
            $informacao = [
                "erro" => false,
                "mensagem" => "Informacao cadastrado!",
                'id_informacao' => $id_informacao,
                'numero_informacao' => $numero_informacao
            ];
            http_response_code(200);
            echo json_encode($informacao);
        }
    }

    public function listarInformacao()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_informacao_list = "SELECT 
        id_informacao, numero_informacao, datElaboracao_informacao, assunto_informacao, executor_informacao, setorElaboracao_informacao, exclusao_informacao, observacao_informacao,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoInformacao, numeracaoDepartamento.nome_departamento as setorInformacao
        FROM numeracaoGabCoordenadorCrhInformacoes
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhInformacoes.assunto_informacao = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhInformacoes.setorElaboracao_informacao = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_informacao = 0 
        ORDER BY id_informacao DESC
        ";

        $result_list_informacao = $this->connect->prepare($query_informacao_list);
        $result_list_informacao->execute();

        if (($result_list_informacao) and ($result_list_informacao->rowCount() != 0)) {
            while ($result_informacao = $result_list_informacao->fetch(PDO::FETCH_ASSOC)) {
                extract($result_informacao);
                $lista_informacao[$id_informacao] = [
                    'id_informacao' => $id_informacao,
                    'numero_informacao' => $numero_informacao,
                    'assunto_informacao' => $assunto_informacao,
                    'assuntoInformacao' => $assuntoInformacao,
                    'datElaboracao_informacao' => date('d/m/Y',strtotime($datElaboracao_informacao)),
                    'executor_informacao' => $executor_informacao,
                    'setorElaboracao_informacao' => $setorElaboracao_informacao,
                    'setorInformacao' => $setorInformacao,
                    'observacao_informacao' => $observacao_informacao
                ];
            }
            http_response_code(200);
            echo json_encode($lista_informacao);
        }
    }

    public function visualizarInformacao($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_informacao = "SELECT
        id_informacao, numero_informacao, datElaboracao_informacao, assunto_informacao, executor_informacao, setorElaboracao_informacao, observacao_informacao,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoInformacao, numeracaoDepartamento.nome_departamento as setorInformacao
        FROM numeracaoGabCoordenadorCrhInformacoes
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhInformacoes.assunto_informacao = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhInformacoes.setorElaboracao_informacao = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_informacao = 0 and id_informacao = :id";

        $result_informacao = $this->connect->prepare($query_visualizar_informacao);
        $result_informacao->bindParam(':id', $id, PDO::PARAM_INT);
        $result_informacao->execute();

        if (($result_informacao) and ($result_informacao->rowCount() != 0)) {
            $row_informacao = $result_informacao->fetch(PDO::FETCH_ASSOC);
            extract($row_informacao);
            $informacao = [
                'id_informacao' => $id_informacao,
                'numero_informacao' => $numero_informacao,
                'assunto_informacao' => $assunto_informacao,
                'assuntoInformacao' => $assuntoInformacao,
                'datElaboracao_informacao' => date('d/m/Y',strtotime($datElaboracao_informacao)),
                'executor_informacao' => $executor_informacao,
                'setorElaboracao_informacao' => $setorElaboracao_informacao,
                'setorInformacao' => $setorInformacao,
                'observacao_informacao' => $observacao_informacao
            ];

            $response = [
                "erro" => false,
                "informacao" => $informacao
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Informacao não encontrada!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarInformacao($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarInformacoes = "UPDATE numeracaoGabCoordenadorCrhInformacoes
        SET assunto_informacao = :assunto_informacao , executor_informacao = :executor_informacao, setorElaboracao_informacao = :setorElaboracao_informacao, 
        observacao_informacao = :observacao_informacao 
        WHERE id_informacao = :id_informacao ";

        $editInformacao = $this->connect->prepare($query_editarInformacoes);
        $editInformacao->bindParam(':assunto_informacao', $dados['assunto_informacao'], PDO::PARAM_INT);
        $editInformacao->bindParam(':executor_informacao', $dados['executor_informacao'], PDO::PARAM_STR);
        $editInformacao->bindParam(':setorElaboracao_informacao', $dados['setorElaboracao_informacao'], PDO::PARAM_INT);
        $editInformacao->bindParam(':observacao_informacao', $dados['observacao_informacao'], PDO::PARAM_STR);
        $editInformacao->bindParam(':id_informacao', $dados['id_informacao'], PDO::PARAM_INT);

        $editInformacao->execute();
        
    }

    public function newListarInformacao($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_informacao = $this->listarInformacao();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_informacao, numero_informacao, datElaboracao_informacao, assunto_informacao, executor_informacao, setorElaboracao_informacao, observacao_informacao,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoInformacao, numeracaoDepartamento.nome_departamento as setorInformacao
            FROM numeracaoGabCoordenadorCrhInformacoes
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhInformacoes.assunto_informacao = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhInformacoes.setorElaboracao_informacao = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_informacao = 0  and numero_informacao like :numero_informacao OR
            exclusao_informacao = 0  and executor_informacao like :executor_informacao OR
            exclusao_informacao = 0  and assunto_informacao like :assunto_informacao OR  
            exclusao_informacao = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntoInformacao OR
            exclusao_informacao = 0  and numeracaoDepartamento.nome_departamento LIKE :setorInformacao
            ORDER BY id_informacao DESC";

            $listar_informacao = $this->connect->prepare($newListar);
            $listar_informacao->bindParam(':numero_informacao', $ParLike, PDO::PARAM_INT);
            $listar_informacao->bindParam(':executor_informacao', $ParLike, PDO::PARAM_STR);
            $listar_informacao->bindParam(':assunto_informacao', $ParLike, PDO::PARAM_INT);
            $listar_informacao->bindParam(':assuntoInformacao', $ParLike, PDO::PARAM_STR);
            $listar_informacao->bindParam(':setorInformacao', $ParLike, PDO::PARAM_STR);
            $listar_informacao->execute();

            if (($listar_informacao) and ($listar_informacao->rowCount() != 0)) {
                while ($result_informacao = $listar_informacao->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_informacao);
                    $lista_informacao[$id_informacao] = [
                        'id_informacao' => $id_informacao,
                        'numero_informacao' => $numero_informacao,
                        'assunto_informacao' => $assunto_informacao,
                        'assuntoInformacao' => $assuntoInformacao,
                        'datElaboracao_informacao' => $datElaboracao_informacao,
                        'executor_informacao' => $executor_informacao,
                        'setorElaboracao_informacao' => $setorElaboracao_informacao,
                        'setorInformacao' => $setorInformacao,
                        'observacao_informacao' => $observacao_informacao
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_informacao);
            }
        }
    }

    public function excluirInformacao($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_informacao_list = "UPDATE numeracaoGabCoordenadorCrhInformacoes
        SET automaticoExclusao_informacao = GETDATE(), exclusao_informacao = 1 
        WHERE id_informacao= :id";

        $exclusaoInformacao = $this->connect->prepare($query_informacao_list);
        $exclusaoInformacao->bindParam(':id', $dados['id_informacao']);
        $exclusaoInformacao->execute();
    }
}
