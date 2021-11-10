<?php

include_once '../../conexao/Conexao.php';

class MemorandoGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarMemorando($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_memorando = "INSERT INTO numeracaoGabCoordenadorCrhMemorando 
        (datElaboracao_memorando, automaticoCriacao_memorando, anoCriacao_memorando, assunto_memorando, executor_memorando, setorElaboracao_memorando, exclusao_memorando, observacao_memorando)
        VALUES
        (:datElaboracao_memorando, GETDATE(), YEAR(GETDATE()), :assunto_memorando, :executor_memorando, :setorElaboracao_memorando, 0, :observacao_memorando) ";

        $cad_memorando = $this->connect->prepare($query_memorando);

        $cad_memorando->bindParam(':datElaboracao_memorando', $dados['memorando']['datElaboracao_memorando']);
        $cad_memorando->bindParam(':assunto_memorando', $dados['memorando']['assunto_memorando']);
        $cad_memorando->bindParam(':executor_memorando', $dados['memorando']['executor_memorando']);
        $cad_memorando->bindParam(':setorElaboracao_memorando', $dados['memorando']['setorElaboracao_memorando']);
        $cad_memorando->bindParam(':observacao_memorando', $dados['memorando']['observacao_memorando']);
        $cad_memorando->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_memorando, numero_memorando
        FROM numeracaoGabCoordenadorCrhMemorando
        WHERE executor_memorando = :executor_memorando
        ORDER BY id_memorando DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_memorando', $dados['memorando']['executor_memorando']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_memorando =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_memorando);
            $memorando = [
                "erro" => false,
                "mensagem" => "memorando cadastrado!",
                'id_memorando' => $id_memorando,
                'numero_memorando' => $numero_memorando
            ];
            http_response_code(200);
            echo json_encode($memorando);
        }
    }

    public function listarMemorando()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_memorando_list = "SELECT 
        id_memorando, numero_memorando, datElaboracao_memorando, assunto_memorando, executor_memorando, setorElaboracao_memorando, exclusao_memorando, observacao_memorando,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntomemorando, numeracaoDepartamento.nome_departamento as setormemorando
        FROM numeracaoGabCoordenadorCrhMemorando
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhMemorando.assunto_memorando = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhMemorando.setorElaboracao_memorando = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_memorando = 0 
        ORDER BY id_memorando DESC
        ";

        $result_list_memorando = $this->connect->prepare($query_memorando_list);
        $result_list_memorando->execute();

        if (($result_list_memorando) and ($result_list_memorando->rowCount() != 0)) {
            while ($result_memorando = $result_list_memorando->fetch(PDO::FETCH_ASSOC)) {
                extract($result_memorando);
                $lista_memorando[$id_memorando] = [
                    'id_memorando' => $id_memorando,
                    'numero_memorando' => $numero_memorando,
                    'assunto_memorando' => $assunto_memorando,
                    'assuntomemorando' => $assuntomemorando,
                    'datElaboracao_memorando' => date('d/m/Y',strtotime($datElaboracao_memorando)),
                    'executor_memorando' => $executor_memorando,
                    'setorElaboracao_memorando' => $setorElaboracao_memorando,
                    'setormemorando' => $setormemorando,
                    'observacao_memorando' => $observacao_memorando
                ];
            }
            http_response_code(200);
            echo json_encode($lista_memorando);
        }
    }

    public function visualizarMemorando($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_memorando = "SELECT
        id_memorando, numero_memorando, datElaboracao_memorando, assunto_memorando, executor_memorando, setorElaboracao_memorando, observacao_memorando,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntomemorando, numeracaoDepartamento.nome_departamento as setormemorando
        FROM numeracaoGabCoordenadorCrhMemorando
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhMemorando.assunto_memorando = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhMemorando.setorElaboracao_memorando = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_memorando = 0 and id_memorando = :id";

        $result_memorando = $this->connect->prepare($query_visualizar_memorando);
        $result_memorando->bindParam(':id', $id, PDO::PARAM_INT);
        $result_memorando->execute();

        if (($result_memorando) and ($result_memorando->rowCount() != 0)) {
            $row_memorando = $result_memorando->fetch(PDO::FETCH_ASSOC);
            extract($row_memorando);
            $memorando = [
                'id_memorando' => $id_memorando,
                'numero_memorando' => $numero_memorando,
                'assunto_memorando' => $assunto_memorando,
                'assuntomemorando' => $assuntomemorando,
                'datElaboracao_memorando' => date('d/m/Y',strtotime($datElaboracao_memorando)),
                'executor_memorando' => $executor_memorando,
                'setorElaboracao_memorando' => $setorElaboracao_memorando,
                'setormemorando' => $setormemorando,
                'observacao_memorando' => $observacao_memorando
            ];

            $response = [
                "erro" => false,
                "memorando" => $memorando
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "memorando não encontrada!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarMemorando($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarMemorando = "UPDATE numeracaoGabCoordenadorCrhMemorando
        SET assunto_memorando = :assunto_memorando , executor_memorando = :executor_memorando, setorElaboracao_memorando = :setorElaboracao_memorando, 
        observacao_memorando = :observacao_memorando 
        WHERE id_memorando = :id_memorando ";

        $editmemorando = $this->connect->prepare($query_editarMemorando);
        $editmemorando->bindParam(':assunto_memorando', $dados['assunto_memorando'], PDO::PARAM_INT);
        $editmemorando->bindParam(':executor_memorando', $dados['executor_memorando'], PDO::PARAM_STR);
        $editmemorando->bindParam(':setorElaboracao_memorando', $dados['setorElaboracao_memorando'], PDO::PARAM_INT);
        $editmemorando->bindParam(':observacao_memorando', $dados['observacao_memorando'], PDO::PARAM_STR);
        $editmemorando->bindParam(':id_memorando', $dados['id_memorando'], PDO::PARAM_INT);

        $editmemorando->execute();
        
    }

    public function newListarMemorando($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_memorando = $this->listarMemorando();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_memorando, numero_memorando, datElaboracao_memorando, assunto_memorando, executor_memorando, setorElaboracao_memorando, observacao_memorando,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntomemorando, numeracaoDepartamento.nome_departamento as setormemorando
            FROM numeracaoGabCoordenadorCrhMemorando
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhMemorando.assunto_memorando = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhMemorando.setorElaboracao_memorando = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_memorando = 0  and numero_memorando like :numero_memorando OR
            exclusao_memorando = 0  and executor_memorando like :executor_memorando OR
            exclusao_memorando = 0  and assunto_memorando like :assunto_memorando OR  
            exclusao_memorando = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntomemorando OR
            exclusao_memorando = 0  and numeracaoDepartamento.nome_departamento LIKE :setormemorando
            ORDER BY id_memorando DESC";

            $listar_memorando = $this->connect->prepare($newListar);
            $listar_memorando->bindParam(':numero_memorando', $ParLike, PDO::PARAM_INT);
            $listar_memorando->bindParam(':executor_memorando', $ParLike, PDO::PARAM_STR);
            $listar_memorando->bindParam(':assunto_memorando', $ParLike, PDO::PARAM_INT);
            $listar_memorando->bindParam(':assuntomemorando', $ParLike, PDO::PARAM_STR);
            $listar_memorando->bindParam(':setormemorando', $ParLike, PDO::PARAM_STR);
            $listar_memorando->execute();

            if (($listar_memorando) and ($listar_memorando->rowCount() != 0)) {
                while ($result_memorando = $listar_memorando->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_memorando);
                    $lista_memorando[$id_memorando] = [
                        'id_memorando' => $id_memorando,
                        'numero_memorando' => $numero_memorando,
                        'assunto_memorando' => $assunto_memorando,
                        'assuntomemorando' => $assuntomemorando,
                        'datElaboracao_memorando' => date('d/m/Y',strtotime($datElaboracao_memorando)),
                        'executor_memorando' => $executor_memorando,
                        'setorElaboracao_memorando' => $setorElaboracao_memorando,
                        'setormemorando' => $setormemorando,
                        'observacao_memorando' => $observacao_memorando
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_memorando);
            }
        }
    }

    public function excluirMemorando($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_memorando_list = "UPDATE numeracaoGabCoordenadorCrhMemorando
        SET automaticoExclusao_memorando = GETDATE(), exclusao_memorando = 1 
        WHERE id_memorando= :id";

        $exclusaomemorando = $this->connect->prepare($query_memorando_list);
        $exclusaomemorando->bindParam(':id', $dados['id_memorando']);
        $exclusaomemorando->execute();
    }
}
