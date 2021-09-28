<?php 
 
 include_once '../../conexao/Conexao.php';

 class Memorandos extends Conexao {
     protected object $connect;
     protected $dados;

     public function cadastrarMemorando($dados){
         $conn = new Conexao();
         $this->connect = $conn->conectar();

         $query_cadMemorando = "INSERT INTO numeracaoGadiMemorandos (
             interessado_memorando, assunto_memorando, datEmissao_memorando, automaticoCriacao_memorando, anoCriacao_memorando, executor_memorando,
             setorElaboracao_memorando, observacao_memorando, excluido_memorando, referencia_banquinho
            )
            VALUES (
                :interessado_memorando, :assunto_memorando, :datEmissao_memorando, GETDATE(), YEAR(GETDATE()), :executor_memorando,
                :setorElaboracao_memorando, :observacao_memorando, 0, :referencia_banquinho
            )";

        $cadMemorando = $this->connect->prepare($query_cadMemorando);
        $cadMemorando->bindParam(':interessado_memorando',$dados['memorando']['interessado_memorando'], PDO::PARAM_STR);
        $cadMemorando->bindParam(':assunto_memorando',$dados['memorando']['assunto_memorando'], PDO::PARAM_STR);
        $cadMemorando->bindParam(':datEmissao_memorando',$dados['memorando']['datEmissao_memorando']);
        $cadMemorando->bindParam(':executor_memorando',$dados['memorando']['executor_memorando'], PDO::PARAM_STR);
        $cadMemorando->bindParam(':setorElaboracao_memorando',$dados['memorando']['setorElaboracao_memorando'], PDO::PARAM_INT);
        $cadMemorando->bindParam(':observacao_memorando',$dados['memorando']['observacao_memorando'], PDO::PARAM_STR);
        $cadMemorando->bindParam(':referencia_banquinho',$dados['memorando']['referencia_banquinho'], PDO::PARAM_STR);
        $cadMemorando->execute();

        if($cadMemorando->rowCount()){
            return "CADASTRO REALIZADO COM SUCESSO";
        } else {
            return "CADASTRO NÃO REALIZADO. POR FAVOR, TENTE NOVAMENTE (ERRO 1-B)";
        }
     }

     public function listarMemorandos(){
         
        $conn = new Conexao();
        $this->connect = $conn->conectar();
        
        $query_listMemorando = "SELECT id_memorando, numero_memorando, interessado_memorando, assunto_memorando, datEmissao_memorando, executor_memorando,
        numeracaoSetor.nome_setor as  setor, observacao_memorando, referencia_banquinho 
        FROM numeracaoGadiMemorandos
        INNER JOIN numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiMemorandos.setorElaboracao_memorando
        WHERE excluido_memorando = 0 
        ORDER BY id_memorando DESC ";

        $result_listMemorando = $this->connect->prepare($query_listMemorando);
        $result_listMemorando->execute();

        if (($result_listMemorando)AND ($result_listMemorando->rowCount() !=0)){
            while ($resultMemorando = $result_listMemorando->fetch(PDO::FETCH_ASSOC)){
                extract ($resultMemorando);
                    $lista_memorando[$id_memorando] =[
                    'id_memorando' => $id_memorando,
                    'numero_memorando' => $numero_memorando,
                    'interessado_memorando' => $interessado_memorando,
                    'assunto_memorando' => $assunto_memorando,
                    'datEmissao_memorando' => $datEmissao_memorando,
                    'executor_memorando' => $executor_memorando,
                    'setor' => $setor,
                    'observacao_memorando' => $observacao_memorando
                ];
            }
            http_response_code(200);
            echo json_encode($lista_memorando);
        }

     }

     public function visualizarMemorando($id) {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_memorando_list = "SELECT id_memorando, numero_memorando, interessado_memorando, assunto_memorando, datEmissao_memorando, executor_memorando,
        numeracaoSetor.nome_setor as  setor, observacao_memorando, referencia_banquinho 
        FROM numeracaoGadiMemorandos
        INNER JOIN numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiMemorandos.setorElaboracao_memorando
        WHERE excluido_memorando = 0 AND id_memorando = :id";


        $result_memorando = $this->connect->prepare($query_memorando_list);
        $result_memorando->bindParam(':id',$id, PDO::PARAM_INT);
        $result_memorando->execute();

        if(($result_memorando) AND ($result_memorando->rowCount() != 0)) {
            $row_memorando = $result_memorando->fetch(PDO::FETCH_ASSOC);
            extract($row_memorando);

            $memorando = [
                'id_memorando' => $id_memorando,
                'numero_memorando' => $numero_memorando,
                'interessado_memorando' => $interessado_memorando,
                'assunto_memorando' => $assunto_memorando,
                'datEmissao_memorando' => $datEmissao_memorando,
                'executor_memorando' => $executor_memorando,
                'setor' => $setor,
                'observacao_memorando' => $observacao_memorando
            ];
            $response = [
                "erro" => false,
                "memorando" => $memorando
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Memorando não cadastrado!!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);

     }

     public function editarMemorando($dados) {

         $conn = new Conexao();
         $this->connect = $conn->conectar();
         

         $query_comunicado_list = "UPDATE numeracaoGadiMemorandos
         SET interessado_memorando = :interessado_memorando, assunto_memorando = :assunto_memorando, 
         executor_memorando = :executor_memorando, setorElaboracao_memorando = :setorElaboracao_memorando, observacao_memorando = :observacao_memorando,
         referencia_banquinho = :referencia_banquinho
         WHERE id_memorando = :id AND excluido_memorando = 0" ;

         $editarMemorando = $this->connect->prepare($query_comunicado_list);
         $editarMemorando->bindParam(':interessado_memorando',$dados['interessado_memorando'], PDO::PARAM_STR);
         $editarMemorando->bindParam(':assunto_memorando',$dados['assunto_memorando'], PDO::PARAM_STR);
         $editarMemorando->bindParam(':executor_memorando',$dados['executor_memorando'], PDO::PARAM_STR);
         $editarMemorando->bindParam(':setorElaboracao_memorando',$dados['setorElaboracao_memorando'], PDO::PARAM_INT);
         $editarMemorando->bindParam(':observacao_memorando',$dados['observacao_memorando'], PDO::PARAM_STR);
         $editarMemorando->bindParam(':referencia_banquinho',$dados['referencia_banquinho'], PDO::PARAM_INT);
         $editarMemorando->bindParam(':id',$dados['id_memorando'], PDO::PARAM_INT );

         $editarMemorando->execute();

         ;
     }

     public function excluirMemorando($dados) {

        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_Memorando_list = "UPDATE numeracaoGadiMemorandos
        SET excluido_memorando = 1, automatico_exclusao = GETDATE() WHERE id_memorando = :id";

        $exclusaoMemorando = $this->connect->prepare($query_Memorando_list);
        $exclusaoMemorando->bindParam(':id', $dados['id_memorando']);

        $exclusaoMemorando->execute();

        if($exclusaoMemorando->rowCount()){
            return "Memorando Excluído com Sucesso";
        } else {
            return "Memorando não Excluído, Favor Validar (Error -> 01B)";
        }

     }

     public function newListarMemorando($BuscaFinal) {
         if ($BuscaFinal == null) {
             $BFetchFull = $this->listarMemorandos();
         } else {

             $conn = new Conexao();
             $this->connect = $conn->conectar();

             $ParLike = '%'.$BuscaFinal.'%';

             $BFetch = "SELECT id_memorando, numero_memorando, interessado_memorando, assunto_memorando, datEmissao_memorando, executor_memorando,
             numeracaoSetor.nome_setor as  areaMemorando, observacao_memorando, referencia_banquinho 
             FROM numeracaoGadiMemorandos
             INNER JOIN numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiMemorandos.setorElaboracao_memorando
             WHERE excluido_memorando = 0 AND numero_memorando LIKE :numero_memorando OR
             excluido_memorando = 0 AND interessado_memorando LIKE :interessado_memorando OR
             excluido_memorando = 0 AND assunto_memorando LIKE :assunto_memorando OR
             excluido_memorando = 0 AND numeracaoSetor.nome_setor LIKE :nome_setor OR
             excluido_memorando = 0 AND executor_memorando LIKE :executor_memorando OR
             excluido_memorando = 0 AND referencia_banquinho LIKE :referencia_banquinho
             ORDER BY id_memorando DESC";


             $BFetchFull = $this->connect->prepare($BFetch);

             $BFetchFull->bindParam(':numero_memorando', $ParLike, PDO::PARAM_INT);
             $BFetchFull->bindParam(':interessado_memorando', $ParLike, PDO::PARAM_STR);
             $BFetchFull->bindParam(':assunto_memorando', $ParLike, PDO::PARAM_STR);
             $BFetchFull->bindParam(':executor_memorando', $ParLike, PDO::PARAM_STR);
             $BFetchFull->bindParam(':referencia_banquinho', $ParLike, PDO::PARAM_INT);
             $BFetchFull->bindParam(':nome_setor', $ParLike, PDO::PARAM_INT);
             $BFetchFull->execute();

             $I = 0;  

             if(($BFetchFull) AND ($BFetchFull->rowCount() !=0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                extract($Fetch);           
                            
                    $listaMemorando[$I] = [ 

                        'id_memorando' =>$Fetch['id_memorando'],
                        'numero_memorando' =>$Fetch['numero_memorando'] ,
                        'interessado_memorando' =>$Fetch['interessado_memorando'] ,
                        'assunto_memorando' =>$Fetch['assunto_memorando'],
                        'datEmissao_memorando' =>$Fetch['datEmissao_memorando'],
                        'executor_memorando' =>$Fetch['executor_memorando'],                        
                        'observacao_memorando' =>$Fetch['observacao_memorando'],
                        'referencia_banquinho' =>$Fetch['referencia_banquinho'],
                        'areaMemorando' =>$Fetch['areaMemorando']

                     ];
                $I++;
                }
                http_response_code(200);
                echo json_encode($listaMemorando);
            }
         }
     }
}

