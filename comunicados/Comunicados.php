<?php

include_once '../conexao/Conexao.php';

class Comunicados extends Conexao
{

    public object $connect;
    public $dados;
    public $id;

    
    public function cadastrar($dados)
    {   
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadcomunicados = "INSERT INTO
        tb_comunicados(assunto_comunicado, datEmissao_comunicado, datCriacao_comunicado, executor_comunicado,
        area_comunicado, observacao_comunicado, exclusao)
        VALUES
        (:assunto_comunicado, :datEmissao_comunicado, GETDATE(), :executor_comunicado,
        :area_comunicado, :observacao_comunicado, 0) ";

        $cad_comunicados = $this->connect->prepare($query_cadcomunicados);
        $cad_comunicados->bindParam(':assunto_comunicado',$dados['comunicado']['assunto_comunicado'], PDO::PARAM_STR);
        $cad_comunicados->bindParam(':datEmissao_comunicado',$dados['comunicado']['data_elaboracao']);
        $cad_comunicados->bindParam(':executor_comunicado',$dados['comunicado']['executor_comunicado'], PDO::PARAM_STR);
        $cad_comunicados->bindParam(':area_comunicado',$dados['comunicado']['setor_comunicado'], PDO::PARAM_STR);
        $cad_comunicados->bindParam(':observacao_comunicado',$dados['comunicado']['observacao_comunicado'], PDO::PARAM_STR);

        $cad_comunicados->execute();

        if ($cad_comunicados->rowCount()) {
            return "Cadastro Realizado com Sucesso";
        } else {
            return "Cadastro Não Realizado";
        }
        
    }
    

    public function listar() {
        
        $conn = new Conexao();

        $this->connect = $conn->conectar();
      
        $query_comunicados_list = "SELECT id_comunicado, numero_comunicado, assunto_comunicado, 
        datEmissao_comunicado, executor_comunicado, area_comunicado, 
        observacao_comunicado FROM tb_comunicados WHERE exclusao=0 ORDER BY id_comunicado DESC ";

        $result_list_comunicados = $this->connect->prepare($query_comunicados_list);
        $result_list_comunicados->execute();

        if (($result_list_comunicados) AND ($result_list_comunicados->rowCount() != 0)){
            while ($result_comunicados = $result_list_comunicados->fetch(PDO::FETCH_ASSOC)){
                extract($result_comunicados);
                $lista_comunicados[$id_comunicado] = [
                    'id_comunicado' => $id_comunicado,
                    'numero_comunicado' => $numero_comunicado,
                    'assunto_comunicado' => $assunto_comunicado,
                    'datEmissao_comunicado' => $datEmissao_comunicado,
                    'executor_comunicado' => $executor_comunicado,
                    'area_comunicado' => $area_comunicado,
                    'observacao_comunicado' => $observacao_comunicado
                ];
            }
            //RESPOSTA COM STATUS 200;
            http_response_code(200);
            //RETORNAR OS PROTUDOS EM FORMATO JSON
            echo json_encode($lista_comunicados);
        }   
        
        
    }

    public function visualizar($id) {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_vis_comunicado = "SELECT id_comunicado, numero_comunicado, assunto_comunicado, 
        datEmissao_comunicado, executor_comunicado, area_comunicado, 
        observacao_comunicado FROM tb_comunicados WHERE id_comunicado = :id";

        $result_comunicado = $this->connect->prepare($query_vis_comunicado);
        $result_comunicado->bindParam(':id', $id, PDO::PARAM_INT);
        $result_comunicado->execute();

        if(($result_comunicado) AND ($result_comunicado->rowCount() != 0)) {
            $row_comunicado = $result_comunicado->fetch(PDO::FETCH_ASSOC);
            extract($row_comunicado);

            $comunicado = [
                'id_comunicado' => $id_comunicado,
                'numero_comunicado' => $numero_comunicado,
                'assunto_comunicado' => $assunto_comunicado,
                'datEmissao_comunicado' => $datEmissao_comunicado,
                'executor_comunicado' => $executor_comunicado,
                'area_comunicado' => $area_comunicado,
                'observacao_comunicado' => $observacao_comunicado

            ];

            $response = [
                "erro" => false,
                "comunicado" => $comunicado
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Comunicado não encontrado!"
            ];
        }        
        http_response_code(200);
        echo json_encode($response);
    }

    
    public function editarComunicado($dados) {
        $conn = new Conexao();
        $this->connect = $conn->conectar();        
        
        $query_editComunicado = "UPDATE tb_comunicados 
        SET assunto_comunicado = :assunto_comunicado, datEmissao_comunicado = :datEmissao_comunicado, 
        executor_comunicado = :executor_comunicado, area_comunicado = :area_comunicado,
        observacao_comunicado = :observacao_comunicado WHERE id_comunicado=:id";

        $editComunicado = $this->connect->prepare($query_editComunicado);
        $editComunicado->bindParam(':assunto_comunicado',$dados['assunto_comunicado'], PDO::PARAM_STR);
        $editComunicado->bindParam(':datEmissao_comunicado',$dados['datEmissao_comunicado']);
        $editComunicado->bindParam(':executor_comunicado',$dados['executor_comunicado'], PDO::PARAM_STR);
        $editComunicado->bindParam(':area_comunicado',$dados['area_comunicado'], PDO::PARAM_STR);
        $editComunicado->bindParam(':observacao_comunicado',$dados['observacao_comunicado'], PDO::PARAM_STR);
        $editComunicado->bindParam(':id', $dados['id_comunicado'], PDO::PARAM_INT);

        $editComunicado->execute();
        
        if($editComunicado->rowCount()){
            return "Comunicado Alterado";

        }else {
           return "Comunicado não Editado, Favor Validar (Error -> 01B)";
        }

    }

    public function excluirComunicado($dados){
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_comunicados_list = "UPDATE tb_comunicados
        SET datExclusao = GETDATE(), exclusao = 1 
        WHERE id_comunicado= :id";

        $exclusaoComunicado = $this->connect->prepare($query_comunicados_list);
        $exclusaoComunicado->bindParam(':id', $dados['id_comunicado']);

        $exclusaoComunicado->execute();

        if($exclusaoComunicado->rowCount()){
            return "Despacho Excluído com Sucesso";
        } else {
            return "Despacho não Excluído, Favor Validar (Erro -> 01B)";
        }
    }

    public function newListarComunuicado ($BuscaFinal = null) {

        if ($BuscaFinal == null){
            $BFetchFull = $this->listar();
        } else {

            $conn = new Conexao();
            $this->connect = $conn -> conectar();

            $ParLike = '%'.$BuscaFinal.'%';

            $BFetch = "SELECT id_comunicado, numero_comunicado, assunto_comunicado, datEmissao_comunicado, executor_comunicado, area_comunicado, 
            observacao_comunicado FROM tb_comunicados
            WHERE exclusao=0 AND numero_comunicado LIKE :numero_comunicado OR 
            exclusao=0 AND assunto_comunicado LIKE :assunto_comunicado OR 
            exclusao=0 AND executor_comunicado LIKE :executor_comunicado OR 
            exclusao=0 AND area_comunicado LIKE :area_comunicado 
            ORDER BY id_comunicado DESC";

            $BFetchFull = $this->connect->prepare($BFetch);

            $BFetchFull->bindParam(':numero_comunicado', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':assunto_comunicado', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':executor_comunicado', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':area_comunicado', $ParLike, PDO::PARAM_STR);
            $BFetchFull->execute();

            $I = 0;

            if(($BFetchFull) AND ($BFetchFull->rowCount() !=0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                extract($Fetch);           
                            
                    $listaComunicado[$I] = [ 
                        
                        'id_comunicado' => $id_comunicado,
                        'numero_comunicado' => $numero_comunicado,
                        'assunto_comunicado' => $assunto_comunicado,
                        'datEmissao_comunicado' => $datEmissao_comunicado,
                        'executor_comunicado' => $executor_comunicado,
                        'area_comunicado' => $area_comunicado,
                        'observacao_comunicado' => $observacao_comunicado                  
                     ];
                $I++;
                }
                http_response_code(200);
                echo json_encode($listaComunicado);
                } 
        }
    }    

}
