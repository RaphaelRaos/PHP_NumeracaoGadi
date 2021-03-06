<?php

include_once '../../conexao/Conexao.php';

class Despachos extends Conexao
{

    public object $connect;
    public $dados;

    public function cadastrar($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadDespachos = "INSERT INTO numeracaoGadiDespachos (
        numero_sisrad_processo, interessado_despacho, assunto_despacho, datEmissao_despacho, automaticoCriacao_despacho, anoCriacao_despacho, executor_despacho,  referencia_banquinho,
        setorElaboracao_despacho, observacao_despacho, excluido_despacho, codtabua)
        VALUES
        (:numero_sisrad_processo, :interessado_despacho, :assunto_despacho, :datEmissao_despacho, GETDATE(), YEAR(GETDATE()), :executor_despacho, :referencia_banquinho, 
        :setorElaboracao_despacho, :observacao_despacho, 0, :codtabua)";

        $cad_despachos = $this->connect->prepare($query_cadDespachos);
        $cad_despachos->bindParam(':numero_sisrad_processo', $dados['despacho']['numero_sisrad_processo'], PDO::PARAM_STR);
        $cad_despachos->bindParam(':interessado_despacho', $dados['despacho']['interessado_despacho'], PDO::PARAM_STR);
        $cad_despachos->bindParam(':assunto_despacho', $dados['despacho']['assunto_despacho'], PDO::PARAM_STR);
        $cad_despachos->bindParam(':datEmissao_despacho', $dados['despacho']['datEmissao_despacho']);
        $cad_despachos->bindParam(':executor_despacho', $dados['despacho']['executor_despacho']);
        $cad_despachos->bindParam(':referencia_banquinho', $dados['despacho']['referencia_banquinho']);
        $cad_despachos->bindParam(':setorElaboracao_despacho', $dados['despacho']['setorElaboracao_despacho'], PDO::PARAM_STR);
        $cad_despachos->bindParam(':observacao_despacho', $dados['despacho']['observacao_despacho'], PDO::PARAM_STR);
        $cad_despachos->bindParam(':codtabua', $dados['despacho']['codtabua'], PDO::PARAM_INT);


        $cad_despachos->execute();

        if ($cad_despachos->rowCount()) {
            return "CADASTRO REALIZADO COM SUCESSO";
        } else {
            return "CADASTRO N??O REALIZADO. POR FAVOR, TENTE NOVAMENTE (Erro 1-B)";
        }
    }

    public function listar()
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "SELECT id_despacho, numero_despacho,  numero_sisrad_processo, interessado_despacho, assunto_despacho,
        datEmissao_despacho, automaticoCriacao_despacho, anoCriacao_despacho, executor_despacho,  referencia_banquinho, numeracaoSetor.nome_setor as area_despacho,
        observacao_despacho, excluido_despacho, [Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua,
		[Tabela UGO].[Cod UGO] AS coduo, [Tabela UGO].[Des UGO] AS desuo
		FROM numeracaoGadiDespachos
		INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiDespachos.codtabua
		INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
		INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
		INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiDespachos.setorElaboracao_despacho 
        WHERE excluido_despacho = 0 AND datSaida_despacho IS NULL  
        ORDER BY id_despacho DESC ";

        $result_list_despachos = $this->connect->prepare($query_despacho_list);
        $result_list_despachos->execute();

        if (($result_list_despachos) and ($result_list_despachos->rowCount() != 0)) {
            while ($result_despachos = $result_list_despachos->fetch(PDO::FETCH_ASSOC)) {
                extract($result_despachos);
                $lista_despachos[$id_despacho] = [
                    'id_despacho' => $id_despacho,
                    'numero_despacho' => $numero_despacho,
                    'numero_sisrad_processo' => $numero_sisrad_processo,
                    'interessado_despacho' => $interessado_despacho,
                    'assunto_despacho' => $assunto_despacho,
                    'datEmissao_despacho' => $datEmissao_despacho,
                    'executor_despacho' => $executor_despacho,
                    'area_despacho' => $area_despacho,
                    'observacao_despacho' => $observacao_despacho,
                    'referencia_banquinho'=>$referencia_banquinho,
                    'codua' => $codua,
                    'desua' => $desua,
                    'coduo' => $coduo,
                    'desuo' => $desuo,
                ];
            }
            http_response_code(200);
            echo json_encode($lista_despachos);
        }
    }

    public function visualizar($id)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "SELECT id_despacho, numero_despacho,  numero_sisrad_processo, interessado_despacho, assunto_despacho,
        datEmissao_despacho, automaticoCriacao_despacho, anoCriacao_despacho, executor_despacho,  referencia_banquinho, 
        numeracaoSetor.id_setor as codArea_despacho ,numeracaoSetor.nome_setor as area_despacho,
        observacao_despacho, excluido_despacho, 
        [Tabela UA].CodTabUa as codtabua, [Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua, [Tabela UGO].[Cod UGO] AS coduo, [Tabela UGO].[Des UGO] AS desuo
		FROM numeracaoGadiDespachos
		INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiDespachos.codtabua
		INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
		INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
		INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiDespachos.setorElaboracao_despacho 
        WHERE excluido_despacho = 0 AND datSaida_despacho IS NULL AND id_despacho = :id ORDER BY id_despacho DESC ";

        $result_despacho = $this->connect->prepare($query_despacho_list);
        $result_despacho->bindParam(':id', $id, PDO::PARAM_INT);
        $result_despacho->execute();

        if (($result_despacho) and ($result_despacho->rowCount() != 0)) {
            $row_despacho = $result_despacho->fetch(PDO::FETCH_ASSOC);
            extract($row_despacho);

            $despacho = [
                'id_despacho' => $id_despacho,
                'numero_despacho' => $numero_despacho,
                'numero_sisrad_processo' => $numero_sisrad_processo,
                'interessado_despacho' => $interessado_despacho,
                'assunto_despacho' => $assunto_despacho,
                'datEmissao_despacho' => $datEmissao_despacho,
                'executor_despacho' => $executor_despacho,
                'codArea_despacho' => $codArea_despacho,
                'area_despacho' => $area_despacho,
                'observacao_despacho' => $observacao_despacho,
                'referencia_banquinho'=>$referencia_banquinho,
                'codtabua' => $codtabua,
                'codua' => $codua,
                'desua' => $desua,
                'coduo' => $coduo,
                'desuo' => $desuo
            ];
            $response = [
                "erro" => false,
                "despacho" => $despacho
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Despacho n??o encontrado!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarDespacho($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "UPDATE numeracaoGadiDespachos
        SET numero_sisrad_processo = :numero_sisrad_processo, interessado_despacho = :interessado_despacho, assunto_despacho = :assunto_despacho , executor_despacho = :executor_despacho,
        referencia_banquinho = :referencia_banquinho, setorElaboracao_despacho = :setorElaboracao_despacho, observacao_despacho = :observacao_despacho, codtabua = :codtabua
        WHERE id_despacho = :id AND excluido_despacho = 0";

        $editDespacho = $this->connect->prepare($query_despacho_list);
        $editDespacho->bindParam(':numero_sisrad_processo', $dados['numero_sisrad_processo'], PDO::PARAM_STR);
        $editDespacho->bindParam(':interessado_despacho', $dados['interessado_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':assunto_despacho', $dados['assunto_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':executor_despacho', $dados['executor_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':referencia_banquinho', $dados['referencia_banquinho'], PDO::PARAM_INT);
        $editDespacho->bindParam(':setorElaboracao_despacho', $dados['setorElaboracao_despacho'], PDO::PARAM_INT);
        $editDespacho->bindParam(':observacao_despacho', $dados['observacao_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':codtabua', $dados['codtabua'], PDO::PARAM_INT);
        $editDespacho->bindParam(':id', $dados['id_despacho'], PDO::PARAM_INT);

        $editDespacho->execute();

        if ($editDespacho->rowCount()) {
            return "Despacho Alterado";
        } else {
            return "Despacho n??o Editado, Favor Validar (Error -> 01B)";
        }
    }

    public function excluirDespacho($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "UPDATE numeracaoGadiDespachos 
        SET excluido_despacho = 1, automatico_exclusao = GETDATE() WHERE id_despacho = :id";

        $exclusaoDespacho = $this->connect->prepare($query_despacho_list);
        $exclusaoDespacho->bindParam(':id', $dados['id_despacho']);

        $exclusaoDespacho->execute();

        if ($exclusaoDespacho->rowCount()) {
            return "Despacho Exclu??do com Sucesso";
        } else {
            return "Despacho n??o Exclu??do, Favor Validar (Error -> 01B)";
        }
    }

    public function saidaDespacho($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "UPDATE numeracaoGadiDespachos 
        SET datSaida_despacho =  :datSaida_despacho, automaticoSaida_despacho = GETDATE() WHERE id_despacho = :id";

        $saidaDespacho = $this->connect->prepare($query_despacho_list);
        $saidaDespacho->bindParam(':datSaida_despacho', $dados['datSaida_despacho']);
        $saidaDespacho->bindParam(':id', $dados['id_despacho']);

        $saidaDespacho->execute();

        if ($saidaDespacho->rowCount()) {
            return "Sa??da Realizada com Sucesso";
        } else {
            return "Sa??da n??o realizada, Favor Validar (Error -> 01B)";
        }
    }

    public function newListarDespachos($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $BFetchFull = $this->listar();
        } else {
            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';

            $BFetch = "SELECT id_despacho, numero_despacho,  numero_sisrad_processo, interessado_despacho, assunto_despacho,
            datEmissao_despacho, automaticoCriacao_despacho, anoCriacao_despacho, executor_despacho,  referencia_banquinho, numeracaoSetor.nome_setor as area_despacho,
            observacao_despacho, [Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua,
            [Tabela UGO].[Cod UGO] AS coduo, [Tabela UGO].[Des UGO] AS desuo
            FROM numeracaoGadiDespachos
            INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiDespachos.codtabua
            INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
            INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
            INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiDespachos.setorElaboracao_despacho 
            WHERE excluido_despacho = 0 AND datSaida_despacho IS NULL AND numero_despacho LIKE :numero_despacho OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND numero_sisrad_processo LIKE :numero_sisrad_processo OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND interessado_despacho LIKE :interessado_despacho OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND referencia_banquinho LIKE :referencia_banquinho OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND [Tabela UA].[Des UA] LIKE :desua OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND numeracaoSetor.nome_setor LIKE :nome_setor OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND assunto_despacho LIKE :assunto_despacho OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND executor_despacho LIKE :executor_despacho 
            ORDER BY id_despacho DESC ";

            $BFetchFull = $this->connect->prepare($BFetch);

            $BFetchFull->bindParam(':numero_despacho', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':numero_sisrad_processo', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':interessado_despacho', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':assunto_despacho', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':executor_despacho', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':referencia_banquinho', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':desua', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':nome_setor', $ParLike, PDO::PARAM_STR);
            $BFetchFull->execute();

            $I = 0;

            if (($BFetchFull) and ($BFetchFull->rowCount() != 0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                    extract($Fetch);

                    $listaDespacho[$I] = [
                        'id_despacho' => $Fetch['id_despacho'],
                        'numero_despacho' => $Fetch['numero_despacho'],
                        'numero_sisrad_processo' => $Fetch['numero_sisrad_processo'],
                        'interessado_despacho' => $Fetch['interessado_despacho'],
                        'assunto_despacho' => $Fetch['assunto_despacho'],
                        'datEmissao_despacho' => $Fetch['datEmissao_despacho'],
                        'executor_despacho' => $Fetch['executor_despacho'],
                        'observacao_despacho' => $Fetch['observacao_despacho'],
                        'UNIDADE' => $Fetch['desua'],
                        'desuo' => $Fetch['desuo'],
                        'referencia_banquinho' => $Fetch['referencia_banquinho'],
                        'area_comunicado' => $Fetch['area_comunicado']
                    ];
                    $I++;
                }
                http_response_code(200);
                echo json_encode($listaDespacho);
            }
        }
    }
}
