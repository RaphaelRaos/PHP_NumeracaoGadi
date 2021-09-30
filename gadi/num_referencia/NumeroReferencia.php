<?php

include_once '../../conexao/Conexao.php';

class NumeroReferencia extends Conexao
{
    protected object $connect;
    protected $dados;

    public function cadastrarReferencia($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadReferencia = "INSERT INTO numeracaoGadiNumReferencia 
        (num_processo_referencia,codtabua, interessado_referencia,id_assunto, datEmissao_referencia, automaticoCriacao_referencia, anoCriacao_referencia, executor_referencia,
        setorElaboracao_referencia,andamento, vigencia_referencia, observacao_referencia, excluido_referencia, referencia_banquinho
        )
        VALUES (:num_processo_referencia, :codtabua, :interessado_referencia, :id_assunto, :datEmissao_referencia, GETDATE(), YEAR(GETDATE()), :executor_referencia,
        :setorElaboracao_referencia, :andamento, :vigencia_referencia, :observacao_referencia, 0, :referencia_banquinho
        )";

        $cadReferencia = $this->connect->prepare($query_cadReferencia);
        $cadReferencia->bindParam(':num_processo_referencia', $dados['referencia']['num_processo_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':codtabua', $dados['referencia']['codtabua'], PDO::PARAM_INT);
        $cadReferencia->bindParam(':interessado_referencia', $dados['referencia']['interessado_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':id_assunto', $dados['referencia']['cod_assunto'], PDO::PARAM_INT);
        $cadReferencia->bindParam(':datEmissao_referencia', $dados['referencia']['datEmissao_referencia']);
        $cadReferencia->bindParam(':executor_referencia', $dados['referencia']['executor_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':setorElaboracao_referencia', $dados['referencia']['setorElaboracao_referencia'], PDO::PARAM_INT);
        $cadReferencia->bindParam(':andamento', $dados['referencia']['andamento'], PDO::PARAM_INT);
        $cadReferencia->bindParam(':vigencia_referencia', $dados['referencia']['vigencia_referencia']);
        $cadReferencia->bindParam(':observacao_referencia', $dados['referencia']['observacao_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':referencia_banquinho', $dados['referencia']['referencia_banquinho'], PDO::PARAM_INT);

        $cadReferencia->execute();

        if ($cadReferencia->rowCount()) {
            return "CADASTRO REALIZADO COM SUCESSO";
        } else {
            return "CADASTRO NÃO REALIZADO. POR FAVOR, TENTE NOVAMENTE (ERRO 1-B)";
        }
    }

    public function listarReferencia()
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_listReferencia = "SELECT id_referencia, numero_referencia, num_processo_referencia, interessado_referencia,numeracaoGadiAssuntos.assunto as assuntoReferencia,
        datEmissao_referencia, executor_referencia, numeracaoSetor.nome_setor as area_numReferencia, numeracaoGadiAndamentoProcessos.status_andamento AS statusProcesso, 
        vigencia_referencia, observacao_referencia, excluido_referencia, referencia_banquinho, [Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua, [Tabela UGO].[Cod UGO] AS coduo,
        [Tabela UGO].[Des UGO] AS desuo      
        FROM numeracaoGadiNumReferencia

        INNER JOIN numeracaoGadiAssuntos ON numeracaoGadiNumReferencia.id_assunto = numeracaoGadiAssuntos.id_assunto
        INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiNumReferencia.codtabua
        INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
        INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
        INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiNumReferencia.setorElaboracao_referencia
        INNER JOIN numeracaoGadiAndamentoProcessos ON numeracaoGadiNumReferencia.andamento = numeracaoGadiAndamentoProcessos.id_andamento

        WHERE excluido_referencia = 0  AND automaticoSaida_referencia IS NULL AND  automatico_exclusao IS NULL
        ORDER BY id_referencia DESC ";

        $result_listReferencia = $this->connect->prepare($query_listReferencia);
        $result_listReferencia->execute();

        if (($result_listReferencia) and ($result_listReferencia->rowCount() != 0)) {
            while ($resutReferencia = $result_listReferencia->fetch(PDO::FETCH_ASSOC)) {
                extract($resutReferencia);

                $listaReferencia[$id_referencia] = [
                    'id_referencia' => $id_referencia,
                    'numero_referencia' => $numero_referencia,
                    'num_processo_referencia' => $num_processo_referencia,
                    'interessado_referencia' =>$interessado_referencia,
                    'assuntoReferencia'=>$assuntoReferencia,
                    'datEmissao_referencia' => $datEmissao_referencia,
                    'executor_referencia' => $executor_referencia,
                    'area_numReferencia' => $area_numReferencia,
                    'statusProcesso' => $statusProcesso,
                    'desua' => $desua,
                    'vigencia_referencia' => $vigencia_referencia,
                    'observacao_referencia' => $observacao_referencia,
                    'referencia_banquinho' => $referencia_banquinho
                ];
            }
            http_response_code(200);
            echo json_encode($listaReferencia);
        }
    }

    public function visualizarReferencia($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_referencia_list = "SELECT id_referencia, numero_referencia, num_processo_referencia, interessado_referencia,numeracaoGadiAssuntos.assunto as assuntoReferencia,
        datEmissao_referencia, executor_referencia,
        numeracaoSetor.id_setor as codArea_referencia, numeracaoSetor.nome_setor as area_numReferencia, numeracaoGadiAndamentoProcessos.status_andamento AS statusProcesso, 
        vigencia_referencia, observacao_referencia, excluido_referencia, referencia_banquinho, [Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua, [Tabela UGO].[Cod UGO] AS coduo,
        [Tabela UGO].[Des UGO] AS desuo      
        FROM numeracaoGadiNumReferencia

        INNER JOIN numeracaoGadiAssuntos ON numeracaoGadiNumReferencia.id_assunto = numeracaoGadiAssuntos.id_assunto
        INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiNumReferencia.codtabua
        INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
        INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
        INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiNumReferencia.setorElaboracao_referencia
        INNER JOIN numeracaoGadiAndamentoProcessos ON numeracaoGadiNumReferencia.andamento = numeracaoGadiAndamentoProcessos.id_andamento

        WHERE excluido_referencia = 0  AND automaticoSaida_referencia IS NULL AND  automatico_exclusao IS NULL AND id_referencia = :id
        ORDER BY id_referencia DESC ";

        $result_referencia = $this->connect->prepare($query_referencia_list);
        $result_referencia->bindParam(':id', $id, PDO::PARAM_INT);
        $result_referencia->execute();

        if (($result_referencia) and ($result_referencia->rowCount() != 0)) {
            $row_referencia = $result_referencia->fetch(PDO::FETCH_ASSOC);
            extract($row_referencia);

            $numReferencia = [
                'id_referencia' => $id_referencia,
                'numero_referencia' => $numero_referencia,
                'num_processo_referencia' => $num_processo_referencia,
                'interessado_referencia' =>$interessado_referencia,
                'assuntoReferencia'=>$assuntoReferencia,
                'datEmissao_referencia' => $datEmissao_referencia,
                'executor_referencia' => $executor_referencia,
                'codArea_referencia' => $codArea_referencia,
                'area_numReferencia' => $area_numReferencia,
                'statusProcesso' => $statusProcesso,
                'vigencia_referencia' => $vigencia_referencia,
                'observacao_referencia' => $observacao_referencia,
                'referencia_banquinho' => $referencia_banquinho,
                'desua' => $desua,
                'desuo' => $desuo
            ];
            $response = [
                "erro" => false,
                "numeroReferencia" => $numReferencia
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Número de Referência não Cadastrado!!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarReferencia($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_referencia_list = "UPDATE numeracaoGadiNumReferencia
        SET num_processo_referencia = :num_processo_referencia ,codtabua = :codtabua, interessado_referencia = :interessado_referencia,id_assunto = :id_assunto,
        executor_referencia = :executor_referencia, setorElaboracao_referencia = :setorElaboracao_referencia , andamento =:andamento, 
        vigencia_referencia= :vigencia_referencia, observacao_referencia = :observacao_referencia,referencia_banquinho = :referencia_banquinho, 
        motivoDevolucao_referencia = :motivoDevolucao_referencia
        WHERE id_referencia = :id AND excluido_referencia = 0";

        $editReferencia = $this->connect->prepare($query_referencia_list);
        $editReferencia->bindParam(':num_processo_referencia', $dados['num_processo_referencia'], PDO::PARAM_STR);
        $editReferencia->bindParam(':codtabua', $dados['codtabua'], PDO::PARAM_INT);
        $editReferencia->bindParam(':interessado_referencia', $dados['interessado_referencia'], PDO::PARAM_STR);
        $editReferencia->bindParam(':id_assunto', $dados['id_assunto'], PDO::PARAM_INT);
        $editReferencia->bindParam(':executor_referencia', $dados['executor_referencia'], PDO::PARAM_STR);
        $editReferencia->bindParam(':setorElaboracao_referencia', $dados['setorElaboracao_referencia'], PDO::PARAM_INT);
        $editReferencia->bindParam(':andamento', $dados['andamento'], PDO::PARAM_INT);
        $editReferencia->bindParam(':vigencia_referencia', $dados['vigencia_referencia']);
        $editReferencia->bindParam(':observacao_referencia', $dados['observacao_referencia'], PDO::PARAM_STR);
        $editReferencia->bindParam(':referencia_banquinho', $dados['referencia_banquinho'], PDO::PARAM_INT);
        $editReferencia->bindParam(':motivoDevolucao_referencia', $dados['motivoDevolucao_referencia'], PDO::PARAM_INT);
        $editReferencia->bindParam(':id', $dados['id_referencia']);

        $editReferencia->execute();

        if ($editReferencia->rowCount()) {
            return "Número de Referência Alterado";
        } else {
            return "Número de Referência não Editado, Favor Validar (Error -> 01B)";
        }
    }

    public function excluirReferencia($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_listReferencia = "UPDATE numeracaoGadiNumReferencia
        SET excluido_referencia = 1, automatico_exclusao = GETDATE() WHERE id_referencia = :id";

        $exclusaoReferencia = $this->connect->prepare($query_listReferencia);
        $exclusaoReferencia->bindParam(':id', $dados['id_referencia']);

        $exclusaoReferencia->execute();

        if ($exclusaoReferencia->rowCount()) {
            return "Número de Referência Excluído com Sucesso";
        } else {
            return "Número de Referência não excluído, Favor Validar (Error -> 01B)";
        }
    }

    public function saidaReferencia($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_referencia_list = "UPDATE numeracaoGadiNumReferencia 
        SET datSaida_referencia =  :datSaida_referencia, automaticoSaida_referencia = GETDATE(), motivoDevolucao_referencia = :motivoDevolucao_referencia
        WHERE id_referencia = :id";

        $saidaReferencia = $this->connect->prepare($query_referencia_list);
        $saidaReferencia->bindParam('motivoDevolucao_referencia', $dados['motivoDevolucao_referencia']);
        $saidaReferencia->bindParam(':datSaida_referencia', $dados['datSaida_referencia']);
        $saidaReferencia->bindParam(':id', $dados['id_referencia']);

        $saidaReferencia->execute();

        if ($saidaReferencia->rowCount()) {
            return "Saída Realizada com Sucesso";
        } else {
            return "Saída não realizada, Favor Validar (Error -> 01B)";
        }
    }


    public function newListarReferencia($BuscaFinal)
    {

        if ($BuscaFinal == null) {
            $BFetchFull = $this->listarReferencia();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';

            $BFetch = "SELECT id_referencia, numero_referencia, num_processo_referencia, interessado_referencia,numeracaoGadiAssuntos.assunto as assuntoReferencia,
            datEmissao_referencia, executor_referencia, numeracaoSetor.nome_setor as area_numReferencia, numeracaoGadiAndamentoProcessos.status_andamento AS statusProcesso, 
            vigencia_referencia, observacao_referencia, excluido_referencia, referencia_banquinho, [Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua, 
            [Tabela UGO].[Cod UGO] AS coduo, [Tabela UGO].[Des UGO] AS desuo      
            FROM numeracaoGadiNumReferencia
    
            INNER JOIN numeracaoGadiAssuntos ON numeracaoGadiNumReferencia.id_assunto = numeracaoGadiAssuntos.id_assunto
            INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiNumReferencia.codtabua
            INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
            INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
            INNER JOIN numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiNumReferencia.setorElaboracao_referencia
            INNER JOIN numeracaoGadiAndamentoProcessos ON numeracaoGadiNumReferencia.andamento = numeracaoGadiAndamentoProcessos.id_andamento
    
            WHERE excluido_referencia = 0 AND automaticoSaida_referencia IS NULL AND automatico_exclusao IS NULL AND numero_referencia LIKE :numero_referencia OR
                excluido_referencia = 0 AND automaticoSaida_referencia IS NULL AND automatico_exclusao IS NULL AND num_processo_referencia LIKE :num_processo_referencia OR
                excluido_referencia = 0 AND automaticoSaida_referencia IS NULL AND automatico_exclusao IS NULL AND interessado_referencia LIKE :interessado_referencia OR
                excluido_referencia = 0 AND automaticoSaida_referencia IS NULL AND automatico_exclusao IS NULL AND referencia_banquinho LIKE :referencia_banquinho OR
                excluido_referencia = 0 AND automaticoSaida_referencia IS NULL AND automatico_exclusao IS NULL AND [Tabela UA].[Des UA] LIKE :desua OR
                excluido_referencia = 0 AND automaticoSaida_referencia IS NULL AND automatico_exclusao IS NULL AND numeracaoSetor.nome_setor LIKE :nome_setor OR
                excluido_referencia = 0 AND automaticoSaida_referencia IS NULL AND automatico_exclusao IS NULL AND numeracaoGadiAssuntos.assunto LIKE :assunto 
           
            ORDER BY id_referencia DESC ";

            $BFetchFull = $this->connect->prepare($BFetch);

            $BFetchFull->bindParam(':numero_referencia', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':num_processo_referencia', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':interessado_referencia', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':referencia_banquinho', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':desua', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':nome_setor', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':assunto', $ParLike, PDO::PARAM_STR);
            $BFetchFull->execute();

            $I = 0;

            if (($BFetchFull) and ($BFetchFull->rowCount() != 0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                    extract($Fetch);

                    $listaReferencia[$I] = [

                        'id_referencia' => $Fetch['id_referencia'],
                        'numero_referencia' => $Fetch['numero_referencia'],
                        'num_processo_referencia' => $Fetch['num_processo_referencia'],
                        'interessado_referencia' =>$Fetch['interessado_referencia'],
                        'assuntoReferencia' =>$Fetch['assuntoReferencia'],                        
                        'datEmissao_referencia' => $Fetch['datEmissao_referencia'],
                        'executor_referencia' => $Fetch['executor_referencia'],
                        'area_numReferencia' => $Fetch['area_numReferencia'],
                        'statusProcesso' => $Fetch['statusProcesso'],
                        'desua' => $Fetch['desua'],
                        'vigencia_referencia' => $Fetch['vigencia_referencia'],
                        'observacao_referencia' => $Fetch['observacao_referencia'],
                        'referencia_banquinho' => $Fetch['referencia_banquinho']
                    ];                 
                    $I++;
                }
                http_response_code(200);
                echo json_encode($listaReferencia);
            }
        }
    }
}
