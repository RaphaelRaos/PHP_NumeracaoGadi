<?php

include_once '../../conexao/Conexao.php';

class InstrucaoGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarInstrucao($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_instrucao = "INSERT INTO numeracaoGabCoordenadorCrhInstrucoes 
        (datElaboracao_instrucao, automaticoCriacao_instrucao, anoCriacao_instrucao, assunto_instrucao, executor_instrucao, setorElaboracao_instrucao, exclusao_instrucao, observacao_instrucao)
        VALUES
        (:datElaboracao_instrucao, GETDATE(), YEAR(GETDATE()), :assunto_instrucao, :executor_instrucao, :setorElaboracao_instrucao, 0, :observacao_instrucao) ";

        $cad_instrucao = $this->connect->prepare($query_instrucao);

        $cad_instrucao->bindParam(':datElaboracao_instrucao', $dados['instrucao']['datElaboracao_instrucao']);
        $cad_instrucao->bindParam(':assunto_instrucao', $dados['instrucao']['assunto_instrucao']);
        $cad_instrucao->bindParam(':executor_instrucao', $dados['instrucao']['executor_instrucao']);
        $cad_instrucao->bindParam(':setorElaboracao_instrucao', $dados['instrucao']['setorElaboracao_instrucao']);
        $cad_instrucao->bindParam(':observacao_instrucao', $dados['instrucao']['observacao_instrucao']);
        $cad_instrucao->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_instrucao, numero_instrucao
        FROM numeracaoGabCoordenadorCrhInstrucoes
        WHERE executor_instrucao = :executor_instrucao
        ORDER BY id_instrucao DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_instrucao', $dados['instrucao']['executor_instrucao']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_instrucao =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_instrucao);
            $instrucao = [
                "erro" => false,
                "mensagem" => "instrucao cadastrada!",
                'id_instrucao' => $id_instrucao,
                'numero_instrucao' => $numero_instrucao
            ];
            http_response_code(200);
            echo json_encode($instrucao);
        }
    }

    public function listarInstrucao()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_instrucao_list = "SELECT 
        id_instrucao, numero_instrucao, datElaboracao_instrucao, assunto_instrucao, executor_instrucao, setorElaboracao_instrucao, exclusao_instrucao, observacao_instrucao,
        numeracaoDepartamento.nome_departamento as setorinstrucao
        FROM numeracaoGabCoordenadorCrhInstrucoes
       
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhInstrucoes.setorElaboracao_instrucao = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_instrucao = 0 
        ORDER BY id_instrucao DESC
        ";

        $result_list_instrucao = $this->connect->prepare($query_instrucao_list);
        $result_list_instrucao->execute();

        if (($result_list_instrucao) and ($result_list_instrucao->rowCount() != 0)) {
            while ($result_instrucao = $result_list_instrucao->fetch(PDO::FETCH_ASSOC)) {
                extract($result_instrucao);
                $lista_instrucao[$id_instrucao] = [
                    'id_instrucao' => $id_instrucao,
                    'numero_instrucao' => $numero_instrucao,
                    'assunto_instrucao' => $assunto_instrucao,
                    'datElaboracao_instrucao' => $datElaboracao_instrucao,
                    'executor_instrucao' => $executor_instrucao,
                    'setorElaboracao_instrucao' => $setorElaboracao_instrucao,
                    'setorinstrucao' => $setorinstrucao,
                    'observacao_instrucao' => $observacao_instrucao
                ];
            }
            http_response_code(200);
            echo json_encode($lista_instrucao);
        }
    }

    public function visualizarInstrucao($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_instrucao = "SELECT
        id_instrucao, numero_instrucao, datElaboracao_instrucao, assunto_instrucao, executor_instrucao, setorElaboracao_instrucao, observacao_instrucao,
        numeracaoDepartamento.nome_departamento as setorinstrucao
        FROM numeracaoGabCoordenadorCrhInstrucoes
       
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhInstrucoes.setorElaboracao_instrucao = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_instrucao = 0 and id_instrucao = :id";

        $result_instrucao = $this->connect->prepare($query_visualizar_instrucao);
        $result_instrucao->bindParam(':id', $id, PDO::PARAM_INT);
        $result_instrucao->execute();

        if (($result_instrucao) and ($result_instrucao->rowCount() != 0)) {
            $row_instrucao = $result_instrucao->fetch(PDO::FETCH_ASSOC);
            extract($row_instrucao);
            $instrucao = [
                'id_instrucao' => $id_instrucao,
                'numero_instrucao' => $numero_instrucao,
                'assunto_instrucao' => $assunto_instrucao,
                'datElaboracao_instrucao' => date('d/m/Y',strtotime($datElaboracao_instrucao)),
                'executor_instrucao' => $executor_instrucao,
                'setorElaboracao_instrucao' => $setorElaboracao_instrucao,
                'setor_instrucao' => $setorinstrucao,
                'observacao_instrucao' => $observacao_instrucao
            ];

            $response = [
                "erro" => false,
                "instrucao" => $instrucao
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "instrucao não encontrada!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarInstrucao($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarInstrucoes = "UPDATE numeracaoGabCoordenadorCrhInstrucoes
        SET assunto_instrucao = :assunto_instrucao , executor_instrucao = :executor_instrucao, setorElaboracao_instrucao = :setorElaboracao_instrucao, 
        observacao_instrucao = :observacao_instrucao 
        WHERE id_instrucao = :id_instrucao ";

        $editinstrucao = $this->connect->prepare($query_editarInstrucoes);
        $editinstrucao->bindParam(':assunto_instrucao', $dados['assunto_instrucao'], PDO::PARAM_INT);
        $editinstrucao->bindParam(':executor_instrucao', $dados['executor_instrucao'], PDO::PARAM_STR);
        $editinstrucao->bindParam(':setorElaboracao_instrucao', $dados['setorElaboracao_instrucao'], PDO::PARAM_INT);
        $editinstrucao->bindParam(':observacao_instrucao', $dados['observacao_instrucao'], PDO::PARAM_STR);
        $editinstrucao->bindParam(':id_instrucao', $dados['id_instrucao'], PDO::PARAM_INT);

        $editinstrucao->execute();
        
    }

    public function newListarInstrucao($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_instrucao = $this->listarInstrucao();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_instrucao, numero_instrucao, datElaboracao_instrucao, assunto_instrucao, executor_instrucao, setorElaboracao_instrucao, observacao_instrucao,
            numeracaoDepartamento.nome_departamento as setorinstrucao
            FROM numeracaoGabCoordenadorCrhInstrucoes           
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhInstrucoes.setorElaboracao_instrucao = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_instrucao = 0  and numero_instrucao like :numero_instrucao OR
            exclusao_instrucao = 0  and executor_instrucao like :executor_instrucao OR
            exclusao_instrucao = 0  and assunto_instrucao like :assunto_instrucao OR  
            exclusao_instrucao = 0  and numeracaoDepartamento.nome_departamento LIKE :setorinstrucao
            ORDER BY id_instrucao DESC";

            $listar_instrucao = $this->connect->prepare($newListar);
            $listar_instrucao->bindParam(':numero_instrucao', $ParLike, PDO::PARAM_INT);
            $listar_instrucao->bindParam(':executor_instrucao', $ParLike, PDO::PARAM_STR);
            $listar_instrucao->bindParam(':assunto_instrucao', $ParLike, PDO::PARAM_STR);            
            $listar_instrucao->bindParam(':setorinstrucao', $ParLike, PDO::PARAM_STR);
            $listar_instrucao->execute();

            if (($listar_instrucao) and ($listar_instrucao->rowCount() != 0)) {
                while ($result_instrucao = $listar_instrucao->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_instrucao);
                    $lista_instrucao[$id_instrucao] = [
                        'id_instrucao' => $id_instrucao,
                        'numero_instrucao' => $numero_instrucao,
                        'assunto_instrucao' => $assunto_instrucao,
                        'datElaboracao_instrucao' => date('d/m/Y',strtotime($datElaboracao_instrucao)),
                        'executor_instrucao' => $executor_instrucao,
                        'setorElaboracao_instrucao' => $setorElaboracao_instrucao,
                        'setorinstrucao' => $setorinstrucao,
                        'observacao_instrucao' => $observacao_instrucao
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_instrucao);
            }
        }
    }

    public function excluirInstrucao($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_instrucao_list = "UPDATE numeracaoGabCoordenadorCrhInstrucoes
        SET automaticoExclusao_instrucao = GETDATE(), exclusao_instrucao = 1 
        WHERE id_instrucao= :id";

        $exclusaoinstrucao = $this->connect->prepare($query_instrucao_list);
        $exclusaoinstrucao->bindParam(':id', $dados['id_instrucao']);
        $exclusaoinstrucao->execute();
    }
}
