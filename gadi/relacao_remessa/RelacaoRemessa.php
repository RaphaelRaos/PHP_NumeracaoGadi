<?php


    include_once '../../conexao/Conexao.php';

class RelacaoRemessa extends Conexao {

    protected object $connect;
    protected $dados;

    public function cadastrarRemessa($dados){

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadRemessa = "INSERT INTO numeracaoGadiRemessas 
        (        
        numProcesso_remessa, codtabua, interessado_remessa ,assunto_remessa ,datEmissao_remessa ,automaticoCriacao_remessa, anoCriacao_remessa,executor_remessa,
        setorElaboracao_remessa, observacao_remessa, excluido_remessa, referencia_banquinho
        )
        VALUES
        (
        :numProcesso_remessa, :codtabua, :interessado_remessa, :assunto_remessa, :datEmissao_remessa, GETDATE(), YEAR(GETDATE()), :executor_remessa,
        :setorElaboracao_remessa, :observacao_remessa, 0, :referencia_banquinho
        )";

        $cadRemessa = $this->connect->prepare($query_cadRemessa);
        $cadRemessa->bindParam(':numProcesso_remessa',$dados['remessa']['numProcesso_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':codtabua',$dados['remessa']['codtabua'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':interessado_remessa',$dados['remessa']['interessado_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':assunto_remessa',$dados['remessa']['assunto_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':datEmissao_remessa',$dados['remessa']['datEmissao_remessa']);
        $cadRemessa->bindParam(':executor_remessa',$dados['remessa']['executor_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':setorElaboracao_remessa',$dados['remessa']['setorElaboracao_remessa'], PDO::PARAM_INT);
        $cadRemessa->bindParam(':observacao_remessa',$dados['remessa']['observacao_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':referencia_banquinho',$dados['remessa']['referencia_banquinho'], PDO::PARAM_INT);
        
        $cadRemessa->execute();

        if($cadRemessa->rowCount()){
            return 'CADASTRO REALIZADO COM SUCESSO';
        }else {
            return 'CADASTRO NÃO REALIZADO. POR FAVOR, TENTE NOVAMENTE (1-B)';
        }
    } 

    public function listarRemessa(){
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_listRemessa = "SELECT
        id_remessa, numero_remessa, numProcesso_remessa, interessado_remessa ,assunto_remessa ,datEmissao_remessa ,executor_remessa, numeracaoSetor.nome_setor as area_remessa,
        observacao_remessa, referencia_banquinho,[Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua, [Tabela UGO].[Cod UGO] AS coduo, [Tabela UGO].[Des UGO] AS desuo 
        
        FROM numeracaoGadiRemessas
        INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiRemessas.codtabua
		INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
		INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
		INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiRemessas.setorElaboracao_remessa 
        WHERE excluido_remessa = 0
        ORDER BY id_remessa DESC";

        $result_listRemessa = $this->connect->prepare ($query_listRemessa);
        $result_listRemessa->execute();

        if (($result_listRemessa) AND ($result_listRemessa->rowCount() !=0)) {
            while ($resultRemessa = $result_listRemessa->fetch(PDO::FETCH_ASSOC)){
                extract ($resultRemessa);

                $listaRemessa [$id_remessa] = [
                  'id_remessa' => $id_remessa,
                  'numero_remessa' => $numero_remessa,
                  'numProcesso_remessa' => $numProcesso_remessa,
                  'interessado_remessa' => $interessado_remessa,
                  'assunto_remessa' => $assunto_remessa,
                  'datEmissao_remessa' => $datEmissao_remessa,
                  'executor_remessa' => $executor_remessa,
                  'area_remessa' => $area_remessa,
                  'observacao_remessa' => $observacao_remessa,
                  'referencia_banquinho' => $referencia_banquinho,
                  'codua' => $codua,
                  'desua' => $desua                  
                ];
            }
            http_response_code(200);
            echo json_encode($listaRemessa);
            }
    }

    public function visualizarRemessa($id){
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_listRemessa = "SELECT
        id_remessa, numero_remessa, numProcesso_remessa, interessado_remessa ,assunto_remessa ,datEmissao_remessa ,executor_remessa, 
        numeracaoGadiRemessas.codtabua AS codtabua,
        numeracaoSetor.id_setor as codSetor_remessa, numeracaoSetor.nome_setor as area_remessa,
        observacao_remessa, referencia_banquinho,[Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua, [Tabela UGO].[Cod UGO] AS coduo, [Tabela UGO].[Des UGO] AS desuo 
        
        FROM numeracaoGadiRemessas
        INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiRemessas.codtabua
		INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
		INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
		INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiRemessas.setorElaboracao_remessa 
        WHERE excluido_remessa = 0 AND id_remessa = :id";

        $result_listRemessa = $this->connect->prepare ($query_listRemessa);
        $result_listRemessa->bindParam(':id', $id, PDO::PARAM_INT);
        $result_listRemessa->execute();

        if (($result_listRemessa) AND ($result_listRemessa->rowCount() !=0)) {
            $row_remessa = $result_listRemessa->fetch(PDO::FETCH_ASSOC);
                extract ($row_remessa);

                $listaRemessa =[
                    'id_remessa' => $id_remessa,
                    'numero_remessa' => $numero_remessa,
                    'numProcesso_remessa' => $numProcesso_remessa,
                    'interessado_remessa' => $interessado_remessa,
                    'assunto_remessa' => $assunto_remessa,
                    'datEmissao_remessa' => $datEmissao_remessa,
                    'executor_remessa' => $executor_remessa,
                    'codSetor_remessa' => $codSetor_remessa,
                    'area_remessa' => $area_remessa,
                    'observacao_remessa' => $observacao_remessa,
                    'referencia_banquinho' => $referencia_banquinho,
                    'codtabua' => $codtabua,
                    'codua' => $codua,
                    'desua' => $desua
                ];
                $response = [
                    "erro" => false,
                    "mensagem" => $listaRemessa
                ];
            } else {
                $response = [
                    "erro" => true,
                    "mensagem" => "REMESSA NÃO ENCONTRADO, FAVOR VALIDAR AS INFORMAÇÕES (ERRO 1-B)"
                ];
            }       
            http_response_code(200);
            echo json_encode(($response));
    }

    public function editarRemessa($dados) {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editRemessa = "UPDATE numeracaoGadiRemessas SET         
        numProcesso_remessa = :numProcesso_remessa  , codtabua = :codtabua, interessado_remessa = :interessado_remessa, assunto_remessa = :assunto_remessa,
        executor_remessa = :executor_remessa, setorElaboracao_remessa = :setorElaboracao_remessa  ,observacao_remessa = :observacao_remessa, 
        referencia_banquinho = :referencia_banquinho
        WHERE excluido_remessa = 0 AND id_remessa = :id";

        $editRemessa = $this->connect->prepare($query_editRemessa);
        $editRemessa->bindParam(':numProcesso_remessa',$dados['numProcesso_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':codtabua',$dados['codtabua'], PDO::PARAM_STR);
        $editRemessa->bindParam(':interessado_remessa',$dados['interessado_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':assunto_remessa',$dados['assunto_remessa'], PDO::PARAM_STR);        
        $editRemessa->bindParam(':executor_remessa',$dados['executor_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':setorElaboracao_remessa',$dados['setorElaboracao_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':observacao_remessa',$dados['observacao_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':referencia_banquinho',$dados['referencia_banquinho'], PDO::PARAM_STR);
        $editRemessa->bindParam(':id', $dados['id_remessa'], PDO::PARAM_INT);
        
        $editRemessa->execute();

        if($editRemessa->rowCount()){
            return 'EDIÇÃO REALIZADA COM SUCESSO';
        }else {
            return 'EDIÇÃO NÃO REALIZADA. POR FAVOR, TENTE NOVAMENTE (1-B)';
        }
    }

    public function excluirRemessa ($dados) {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_excluirRemessa = "UPDATE numeracaoGadiRemessas SET         
        excluido_remessa = 1,  automatico_excluido = GETDATE() WHERE id_remessa = :id";

        $excluirRemessa = $this->connect->prepare ($query_excluirRemessa);
        $excluirRemessa->bindParam(':id', $dados['id_remessa'], PDO::PARAM_INT);
        $excluirRemessa->execute();

        if($excluirRemessa->rowCount()){
            return 'EXCLUSAO COM SUCESSO';
        }else {
            return 'EXCLUSAO NÃO REALIZADA. POR FAVOR, TENTE NOVAMENTE (1-B)';
        }
       
    }

    public function newListarRemessa($BuscaFinal) {
        if ($BuscaFinal == null){
            $BFetchFull = $this->listarRemessa();

        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%'.$BuscaFinal.'%';

            $BFetch = "SELECT 
            id_remessa, numero_remessa, numProcesso_remessa, interessado_remessa ,assunto_remessa ,datEmissao_remessa ,executor_remessa, numeracaoSetor.nome_setor as area_remessa,
            observacao_remessa, referencia_banquinho,[Tabela UA].[Cod UA] AS codua,[Tabela UA].[Des UA] AS desua, [Tabela UGO].[Cod UGO] AS coduo, [Tabela UGO].[Des UGO] AS desuo
            
            FROM numeracaoGadiRemessas

            INNER JOIN [Tabela UA] ON [Tabela UA].CodTabUa = numeracaoGadiRemessas.codtabua
            INNER JOIN [TABELA UGE] ON [Tabela UGE].CodTabUGE = [Tabela UA].CodTabUGE
            INNER JOIN [TABELA UGO] ON [Tabela UGO].CodTabUGO = [Tabela UGE].CodTabUGO
            INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiRemessas.setorElaboracao_remessa

            WHERE excluido_remessa = 0 AND numero_remessa LIKE :numero_remessa OR
            excluido_remessa = 0 AND numProcesso_remessa LIKE :numProcesso_remessa OR
            excluido_remessa = 0 AND interessado_remessa LIKE :interessado_remessa OR
            excluido_remessa = 0 AND assunto_remessa LIKE :assunto_remessa OR
            excluido_remessa = 0 AND executor_remessa LIKE :executor_remessa OR
            excluido_remessa = 0 AND referencia_banquinho LIKE :referencia_banquinho OR
            excluido_remessa = 0 AND [Tabela UA].[Des UA] LIKE :desua OR
            excluido_remessa = 0 AND numeracaoSetor.nome_setor LIKE :nome_setor
            
            ORDER BY id_remessa DESC ";

            $BFetchFull = $this->connect->prepare($BFetch);

            $BFetchFull->bindParam(':numero_remessa', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':numProcesso_remessa', $ParLike, PDO::PARAM_STR);    
            $BFetchFull->bindParam(':interessado_remessa', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':assunto_remessa', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':executor_remessa', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':desua', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':nome_setor', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':referencia_banquinho', $ParLike, PDO::PARAM_INT);

            $BFetchFull->execute();            

            $I = 0;   
                
                if(($BFetchFull) AND ($BFetchFull->rowCount() !=0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                extract($Fetch);           
                            
                    $listaRemessa[$I] = [ 
                        
                                'id_remessa' =>$Fetch['id_remessa'] ,
                                'numero_remessa' =>$Fetch['numero_remessa'] ,
                                'numProcesso_remessa' =>$Fetch['numProcesso_remessa'] ,                                
                                'interessado_remessa' =>$Fetch['interessado_remessa'] ,
                                'assunto_remessa' =>$Fetch['assunto_remessa'] ,
                                'datEmissao_remessa' =>$Fetch['datEmissao_remessa'] ,
                                'executor_remessa' =>$Fetch['executor_remessa'] ,
                                'area_remessa' =>$Fetch['area_remessa'] ,
                                'observacao_remessa' =>$Fetch['observacao_remessa'],
                                'referencia_banquinho' =>$Fetch['referencia_banquinho'],
                                'codua' =>$Fetch['codua'],
                                'desua' =>$Fetch['desua']                        
                      ];
                $I++;
                }
                http_response_code(200);
                echo json_encode($listaRemessa);
                } 
        }
    }
}
