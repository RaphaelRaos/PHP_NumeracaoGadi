<?php

include_once '../../conexao/Conexao.php';

class Instrucoes extends Conexao
{

    protected object $connect;
    protected $dados;

    public function cadastrarInstrucao($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadInstrucao = "INSERT INTO numeracaoGadiInstrucoes (
           interessado_instrucao,assunto_instrucao, datEmissao_instrucao, automaticoCriacao_instrucao, anoCriacao_instrucao, executor_instrucao, setorElaboracao_instrucao,
           observacao_instrucao, excluido_instrucao, referencia_banquinho
            )
            VALUES (
            :interessado_instrucao,:assunto_instrucao, :datEmissao_instrucao, GETDATE(), YEAR(GETDATE()), :executor_instrucao, :setorElaboracao_instrucao,
           :observacao_instrucao, 0, :referencia_banquinho
            )";

        $cadInstrucao = $this->connect->prepare($query_cadInstrucao);
        $cadInstrucao->bindParam(':interessado_instrucao', $dados['instrucao']['interessado_instrucao'], PDO::PARAM_STR);
        $cadInstrucao->bindParam(':assunto_instrucao', $dados['instrucao']['assunto_instrucao'], PDO::PARAM_STR);
        $cadInstrucao->bindParam(':datEmissao_instrucao', $dados['instrucao']['datEmissao_instrucao']);
        $cadInstrucao->bindParam(':executor_instrucao', $dados['instrucao']['executor_instrucao'], PDO::PARAM_STR);
        $cadInstrucao->bindParam(':setorElaboracao_instrucao', $dados['instrucao']['setorElaboracao_instrucao']);
        $cadInstrucao->bindParam(':observacao_instrucao', $dados['instrucao']['observacao_instrucao'], PDO::PARAM_STR);
        $cadInstrucao->bindParam(':referencia_banquinho', $dados['instrucao']['referencia_banquinho'], PDO::PARAM_INT);


        $cadInstrucao->execute();

        if ($cadInstrucao->rowCount()) {
            return "CADASTRO REALIZADO COM SUCESSO";
        } else {
            return "CADASTRO NÃO REALIZADO. POR FAVOR, TENTE NOVAMENTE (ERRO 1-B)";
        }
    }

    public function listarInstrucoes()
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_listInstrucao = "SELECT id_instrucao, numero_instrucao, interessado_instrucao,assunto_instrucao, datEmissao_instrucao, executor_instrucao, numeracaoSetor.nome_setor as  setor,
        observacao_instrucao, referencia_banquinho 
        FROM numeracaoGadiInstrucoes 
        INNER JOIN numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiInstrucoes.setorElaboracao_instrucao
        WHERE excluido_instrucao = 0  ORDER BY id_instrucao DESC";

        $result_listInstrucao = $this->connect->prepare($query_listInstrucao);
        $result_listInstrucao->execute();

        if (($result_listInstrucao) and ($result_listInstrucao->rowCount() != 0)) {
            while ($resultInstrucao = $result_listInstrucao->fetch(PDO::FETCH_ASSOC)) {
                extract($resultInstrucao);
                $lista_instrucao[$id_instrucao] = [
                    'id_instrucao' => $id_instrucao,
                    'numero_instrucao' => $numero_instrucao,
                    'interessado_instrucao' => $interessado_instrucao,
                    'assunto_instrucao' => $assunto_instrucao,
                    'datEmissao_instrucao' => $datEmissao_instrucao,
                    'executor_instrucao' => $executor_instrucao,
                    'setor' => $setor,
                    'observacao_instrucao' => $observacao_instrucao,
                    'referencia_banquinho' => $referencia_banquinho
                ];
            }
            http_response_code(200);
            echo json_encode($lista_instrucao);
        }
    }

    public function visualizarInstrucoes($id)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_instrucao_list = "SELECT id_instrucao, numero_instrucao, interessado_instrucao,assunto_instrucao, datEmissao_instrucao, executor_instrucao, numeracaoSetor.nome_setor as  setor,
        observacao_instrucao, referencia_banquinho 
        FROM numeracaoGadiInstrucoes 
        INNER JOIN numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiInstrucoes.setorElaboracao_instrucao
        WHERE excluido_instrucao = 0  AND id_instrucao = :id";

        $resultInstrucao = $this->connect->prepare($query_instrucao_list);
        $resultInstrucao->bindParam(':id', $id, PDO::PARAM_INT);
        $resultInstrucao->execute();

        if (($resultInstrucao) and ($resultInstrucao->rowCount() != 0)) {
            $row_instrucao = $resultInstrucao->fetch(PDO::FETCH_ASSOC);
            extract($row_instrucao);

            $instrucao = [
                'id_instrucao' => $id_instrucao,
                'numero_instrucao' => $numero_instrucao,
                'interessado_instrucao' => $interessado_instrucao,
                'assunto_instrucao' => $assunto_instrucao,
                'datEmissao_instrucao' => $datEmissao_instrucao,
                'executor_instrucao' => $executor_instrucao,
                'setor' => $setor,
                'observacao_instrucao' => $observacao_instrucao,
                'referencia_banquinho' => $referencia_banquinho
            ];
            $response = [
                "erro" => false,
                "instrucao" => $instrucao
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Instrucao não encontrada!!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarInstrucao($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_instrucao_list = "UPDATE numeracaoGadiInstrucoes
        SET interessado_instrucao = :interessado_instrucao , assunto_instrucao = :assunto_instrucao, datEmissao_instrucao = :datEmissao_instrucao, executor_instrucao = :executor_instrucao, 
        setorElaboracao_instrucao = :setorElaboracao_instrucao, observacao_instrucao = :observacao_instrucao, referencia_banquinho = :referencia_banquinho 
        WHERE id_instrucao = :id ";

        $editInstrucao = $this->connect->prepare($query_instrucao_list);
        $editInstrucao->bindParam(':interessado_instrucao', $dados['interessado_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':assunto_instrucao', $dados['assunto_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':datEmissao_instrucao', $dados['datEmissao_instrucao'],);
        $editInstrucao->bindParam(':executor_instrucao', $dados['executor_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':setorElaboracao_instrucao', $dados['setorElaboracao_instrucao'], PDO::PARAM_INT);
        $editInstrucao->bindParam(':observacao_instrucao', $dados['observacao_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':referencia_banquinho', $dados['referencia_banquinho'], PDO::PARAM_INT);
        $editInstrucao->bindParam(':id', $dados['id_instrucao'], PDO::PARAM_INT);

        $editInstrucao->execute();

        if ($editInstrucao->rowCount()) {
            return "Intrução Alterado";
        } else {
            return "Intrução não Editado, Favor Validar (Error -> 01B)";
        }
    }

    public function excluirInstrucao($dados)
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_instrucao_list = "UPDATE numeracaoGadiInstrucoes
        SET excluido_instrucao = 1, automatico_exclusao = GETDATE() WHERE id_instrucao = :id";

        $exclusaoInstrucao = $this->connect->prepare($query_instrucao_list);
        $exclusaoInstrucao->bindParam(':id', $dados['id_instrucao']);

        $exclusaoInstrucao->execute();

        if ($exclusaoInstrucao->rowCount()) {
            return "Instrucao Excluída com Sucesso";
        } else {
            return "Instrucao não Excluída, Favor Validar (Error -> 01B)";
        }
    }

    public function newListarInstrucao($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $BFetchFull = $this->listarInstrucoes();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';

            $BFetch = "SELECT id_instrucao, numero_instrucao, interessado_instrucao,assunto_instrucao, datEmissao_instrucao, executor_instrucao, numeracaoSetor.nome_setor as  setor,
            observacao_instrucao, referencia_banquinho 
            FROM numeracaoGadiInstrucoes 
            INNER JOIN numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiInstrucoes.setorElaboracao_instrucao
            WHERE excluido_instrucao = 0  AND numero_instrucao LIKE :numero_instrucao OR
            excluido_instrucao = 0  AND interessado_instrucao LIKE :interessado_instrucao OR
            excluido_instrucao = 0  AND assunto_instrucao LIKE :assunto_instrucao OR
            excluido_instrucao = 0  AND executor_instrucao LIKE :executor_instrucao OR
            excluido_instrucao = 0  AND referencia_banquinho LIKE :referencia_banquinho
            ORDER BY id_instrucao DESC";

            $BFetchFull = $this->connect->prepare($BFetch);
            $BFetchFull->bindParam(':numero_instrucao', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':interessado_instrucao', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':assunto_instrucao', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':executor_instrucao', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':referencia_banquinho', $ParLike, PDO::PARAM_STR);
            
            $BFetchFull->execute();

            $I = 0;

            if (($BFetchFull) and ($BFetchFull->rowCount() != 0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                    extract($Fetch);

                    $listaIntrucao[$I] = [

                        'id_instrucao' => $Fetch['id_instrucao'],
                        'numero_instrucao' => $Fetch['numero_instrucao'],
                        'interessado_instrucao' => $Fetch['interessado_instrucao'],
                        'assunto_instrucao' => $Fetch['assunto_instrucao'],
                        'datEmissao_instrucao' => $Fetch['datEmissao_instrucao'],
                        'executor_instrucao' => $Fetch['executor_instrucao'],
                        'setor' => $Fetch['setor'],
                        'observacao_instrucao' => $Fetch['observacao_instrucao'],
                        'referencia_banquinho' => $Fetch['referencia_banquinho']
                    ];
                    $I++;
                }
                http_response_code(200);
                echo json_encode($listaIntrucao);
            }
        }
    }
}
