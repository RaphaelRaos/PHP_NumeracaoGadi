<?php

include_once '../conexao/Conexao.php';

class NumeroReferencia extends Conexao {
    protected object $connect;
    protected $dados;

    public function cadastrarReferencia($dados){

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadReferencia = "INSERT INTO tb_numReferencia 
        (num_processo_referencia,des_ua, des_uo,interessado_referencia, assunto, datEntrada_referencia, automatico_entrada,
        executor_referencia, posse_referencia, vigencia_referencia, observacao_referencia, excluido_referencia
        )
        VALUES (:num_processo_referencia,:des_ua,:des_uo, :interessado_referencia, :assunto, :datEntrada_referencia, GETDATE(),
        :executor_referencia, :posse_referencia, :vigencia_referencia, :observacao_referencia, 0
        )";

        $cadReferencia = $this->connect->prepare($query_cadReferencia);
        $cadReferencia->bindParam(':num_processo_referencia',$dados['referencia']['num_processo_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':des_ua',$dados['referencia']['des_ua'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':des_uo',$dados['referencia']['des_uo'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':interessado_referencia',$dados['referencia']['interessado_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':assunto',$dados['referencia']['assunto'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':datEntrada_referencia',$dados['referencia']['datEntrada_referencia']);
        $cadReferencia->bindParam(':executor_referencia',$dados['referencia']['executor_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':posse_referencia',$dados['referencia']['posse_referencia'], PDO::PARAM_STR);
        $cadReferencia->bindParam(':vigencia_referencia',$dados['referencia']['vigencia_referencia']);
        $cadReferencia->bindParam(':observacao_referencia',$dados['referencia']['observacao_referencia'], PDO::PARAM_STR);

        $cadReferencia->execute();

        if($cadReferencia->rowCount()){
            return "CADASTRO REALIZADO COM SUCESSO";
        } else {
            return "CADASTRO NÃO REALIZADO. POR FAVOR, TENTE NOVAMENTE (ERRO 1-B)";
        }
    }

    public function listarReferencia(){
        $conn = new Conexao();
        $this->connect = $conn->conectar();
        
        $query_listReferencia = "SELECT id_referencia, numero_referencia, num_processo_referencia, des_ua, des_uo, assunto, posse_referencia, vigencia_referencia
        FROM tb_numReferencia WHERE excluido_referencia = 0  AND automatico_saida IS NULL ORDER BY id_referencia DESC ";

        $result_listReferencia = $this->connect->prepare($query_listReferencia);
        $result_listReferencia->execute();

        if (($result_listReferencia)AND ($result_listReferencia->rowCount() !=0)){
            while($resutReferencia = $result_listReferencia->fetch(PDO::FETCH_ASSOC)){
                extract($resutReferencia);
                
                $listaReferencia [$id_referencia] = [
                    'id_referencia' => $id_referencia,
                    'numero_referencia' => $numero_referencia,
                    'num_processo_referencia' => $num_processo_referencia,
                    'des_ua' => $des_ua,
                    'des_uo'=> $des_uo,
                    'assunto' => $assunto,
                    'posse_referencia'=> $posse_referencia,
                    'vigencia_referencia' => $vigencia_referencia
                ];
            }
            http_response_code(200);
            echo json_encode($listaReferencia);
        }

    }

    public function visualizarReferencia($id) {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_referencia_list="SELECT 
          id_referencia, numero_referencia, num_processo_referencia, des_ua, des_uo, interessado_referencia, assunto, datEntrada_referencia,
        executor_referencia, posse_referencia, situacao, andamento_referencia, ocorrencia_referencia, vigencia_referencia, status_referencia,
        observacao_referencia from tb_numReferencia WHERE id_referencia = :id"; 

        $result_referencia = $this->connect->prepare($query_referencia_list);
        $result_referencia->bindParam(':id', $id, PDO::PARAM_INT);
        $result_referencia->execute();

        if(($result_referencia) AND ($result_referencia->rowCount() !=0)) {
            $row_referencia = $result_referencia->fetch(PDO::FETCH_ASSOC);
            extract($row_referencia);

            $numReferencia = [
                'id_referencia' => $id_referencia,
                'numero_referencia' => $numero_referencia,
                'num_processo_referencia' => $num_processo_referencia,
                'des_ua' => $des_ua,
                'des_uo' => $des_uo,
                'interessado_referencia' => $interessado_referencia,
                'assunto' => $assunto,
                'datEntrada_referencia' => $datEntrada_referencia,
                'executor_referencia' => $executor_referencia,
                'posse_referencia' => $posse_referencia,
                'situacao' => $situacao,
                'andamento_referencia' => $andamento_referencia,
                'ocorrencia_referencia' => $ocorrencia_referencia,
                'vigencia_referencia' => $vigencia_referencia,
                'status_referencia' => $status_referencia,
                'observacao_referencia' => $observacao_referencia
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

    public function editarReferencia($dados) {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_referencia_list = "UPDATE tb_numReferencia
        SET num_processo_referencia = :num_processo_referencia, des_ua = :des_ua, des_uo =:des_uo, interessado_referencia = :interessado_referencia, assunto = :assunto, 
        executor_referencia = :executor_referencia, posse_referencia = :posse_referencia, situacao = :situacao, andamento_referencia = :andamento_referencia, ocorrencia_referencia = :ocorrencia_referencia,
        vigencia_referencia = :vigencia_referencia, status_referencia = :status_referencia, observacao_referencia = :observacao_referencia WHERE id_referencia = :id
        AND excluido_referencia = 0";

        $editReferencia = $this->connect->prepare($query_referencia_list);
        $editReferencia->bindParam(':num_processo_referencia',$dados['num_processo_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':des_ua',$dados['des_ua'],PDO::PARAM_STR);
        $editReferencia->bindParam(':des_uo',$dados['des_uo'],PDO::PARAM_STR);
        $editReferencia->bindParam(':interessado_referencia',$dados['interessado_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':assunto',$dados['assunto'],PDO::PARAM_STR);
        $editReferencia->bindParam(':executor_referencia',$dados['executor_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':posse_referencia',$dados['posse_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':situacao',$dados['situacao'],PDO::PARAM_STR);
        $editReferencia->bindParam(':andamento_referencia',$dados['andamento_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':ocorrencia_referencia',$dados['ocorrencia_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':vigencia_referencia',$dados['vigencia_referencia']);
        $editReferencia->bindParam(':status_referencia',$dados['status_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':observacao_referencia',$dados['observacao_referencia'],PDO::PARAM_STR);
        $editReferencia->bindParam(':id',$dados['id_referencia']);
        
        $editReferencia->execute();

        if($editReferencia->rowCount()){
            return "Número de Referência Alterado";
        } else {
            return "Número de Referência não Editado, Favor Validar (Error -> 01B)";
        }   
    }

    public function excluirReferencia($dados){
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_listReferencia = "UPDATE tb_numReferencia
        SET excluido_referencia = 1, automatico_exclusao = GETDATE() WHERE id_referencia = :id";

        $exclusaoReferencia = $this->connect->prepare($query_listReferencia);
        $exclusaoReferencia->bindParam(':id',$dados['id_referencia']);

        $exclusaoReferencia->execute();

        if($exclusaoReferencia->rowCount()){
            return "Número de Referência Excluído com Sucesso";
        } else {
            return "Número de Referência não excluído, Favor Validar (Error -> 01B)";
        } 
    }

    public function saidaReferencia($dados){

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_referencia_list = "UPDATE tb_numReferencia 
        SET datSaida_referencia =  :datSaida_referencia, automatico_saida = GETDATE() WHERE id_referencia = :id"; 

        $saidaReferencia = $this->connect->prepare($query_referencia_list);
        $saidaReferencia->bindParam(':datSaida_referencia', $dados['datSaida_referencia']);
        $saidaReferencia->bindParam(':id', $dados['id_referencia']);

        $saidaReferencia->execute();

        if($saidaReferencia->rowCount()){
            return "Saída Realizada com Sucesso";
        } else {
            return "Saída não realizada, Favor Validar (Error -> 01B)";
        }
    }


        public function newListar($BuscaFinal) {

            if ($BuscaFinal == null){
                $BFetchFull = $this->listarReferencia();
            } else {                

                $conn = new Conexao();
                $this->connect = $conn->conectar();

                $ParLike = '%'.$BuscaFinal.'%';

                $BFetch = "SELECT id_referencia, numero_referencia, num_processo_referencia, des_ua, des_uo, assunto, posse_referencia, vigencia_referencia
                FROM tb_numReferencia
                WHERE excluido_referencia = 0 AND automatico_saida IS NULL AND numero_referencia LIKE :numero_referencia OR
                excluido_referencia = 0 AND automatico_saida IS NULL AND num_processo_referencia LIKE :num_processo_referencia OR
                excluido_referencia = 0 AND automatico_saida IS NULL AND assunto LIKE :assunto OR
                excluido_referencia = 0 AND automatico_saida IS NULL AND des_ua LIKE :des_ua
                ORDER BY id_referencia DESC ";
                
                $BFetchFull = $this->connect->prepare($BFetch);
                
                $BFetchFull->bindParam(':numero_referencia', $ParLike, PDO::PARAM_INT);
                $BFetchFull->bindParam(':num_processo_referencia', $ParLike, PDO::PARAM_STR);    
                $BFetchFull->bindParam(':assunto', $ParLike, PDO::PARAM_STR);
                $BFetchFull->bindParam(':des_ua', $ParLike, PDO::PARAM_STR);
                $BFetchFull->execute();

                $I = 0;   
                
                if(($BFetchFull) AND ($BFetchFull->rowCount() !=0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                extract($Fetch);           
                            
                    $listaReferencia[$I] = [ 
                        
                                'id_referencia' =>$Fetch['id_referencia'],
                                'numero_referencia' =>$Fetch['numero_referencia'] ,
                                'num_processo_referencia' =>$Fetch['num_processo_referencia'],
                                'des_ua' =>$Fetch['des_ua'],
                                'des_uo' =>$Fetch['des_uo'],                        
                                'assunto' =>$Fetch['assunto'],    
                                'posse_referencia' =>$Fetch['posse_referencia'],
                                'vigencia_referencia' =>$Fetch['vigencia_referencia']                   
                     ];
                $I++;
                }
                http_response_code(200);
                echo json_encode($listaReferencia);
                } 
            }
    }

            
}   


