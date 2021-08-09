<?php

include_once '../conexao/Conexao.php';

class Instrucoes extends Conexao {

    protected object $connect;
    protected $dados;

    public function cadastrarInstrucao($dados){
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadInstrucao = "INSERT INTO tb_instrucoes (
            interessado_instrucao, assunto_instrucao, datEmissao_instrucao, automatico_emissao, executor_instrucao, setor, observacao_instrucao, excluido_instrucao
            )
            VALUES (
                :interessado_instrucao, :assunto_instrucao, :datEmissao_instrucao, GETDATE(), :executor_instrucao, :setor, :observacao_instrucao, 0
            )";

            $cadInstrucao = $this->connect->prepare($query_cadInstrucao);
            $cadInstrucao->bindParam(':interessado_instrucao',$dados['instrucao']['interessado_instrucao'], PDO::PARAM_STR);
            $cadInstrucao->bindParam(':assunto_instrucao',$dados['instrucao']['assunto_instrucao'], PDO::PARAM_STR);
            $cadInstrucao->bindParam(':datEmissao_instrucao',$dados['instrucao']['datEmissao_instrucao']);
            $cadInstrucao->bindParam(':executor_instrucao',$dados['instrucao']['executor_instrucao'], PDO::PARAM_STR);
            $cadInstrucao->bindParam(':setor',$dados['instrucao']['setor'], PDO::PARAM_STR);
            $cadInstrucao->bindParam(':observacao_instrucao',$dados['instrucao']['observacao_instrucao'], PDO::PARAM_STR);

            $cadInstrucao->execute();

            if($cadInstrucao->rowCount()){
                return "CADASTRO REALIZADO COM SUCESSO";
            }else{
                return "CADASTRO NÃO REALIZADO. POR FAVOR, TENTE NOVAMENTE (ERRO 1-B)";
            }

    }

    public function listarInstrucoes(){
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_listInstrucao = "SELECT id_instrucao, numero_instrucao, interessado_instrucao, assunto_instrucao, 
        datEmissao_instrucao, executor_instrucao, setor, observacao_instrucao FROM tb_instrucoes WHERE excluido_instrucao = 0  ORDER BY id_instrucao DESC";
        
        $result_listInstrucao = $this->connect->prepare($query_listInstrucao);
        $result_listInstrucao->execute();

        if (($result_listInstrucao)AND ($result_listInstrucao->rowCount() !=0)){
            while ($resultInstrucao = $result_listInstrucao->fetch(PDO::FETCH_ASSOC)){
                extract ($resultInstrucao);
                $lista_instrucao ['registro_instrucao'][$id_instrucao] =[
                    'id_instrucao' => $id_instrucao,
                    'numero_instrucao' => $numero_instrucao,
                    'interessado_instrucao' => $interessado_instrucao,
                    'assunto_instrucao' => $assunto_instrucao,
                    'datEmissao_instrucao' => $datEmissao_instrucao,
                    'executor_instrucao' => $executor_instrucao,
                   'setor' => $setor,
                    'observacao_instrucao' => $observacao_instrucao
                ];
            }
            http_response_code(200);
            echo json_encode($lista_instrucao);
        }
    }

    public function visualizarInstrucoes($id){

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_instrucao_list = "SELECT id_instrucao, numero_instrucao, interessado_instrucao, assunto_instrucao, 
        datEmissao_instrucao, executor_instrucao, setor, observacao_instrucao FROM tb_instrucoes WHERE excluido_instrucao = 0 AND id_instrucao = :id";

        $resultInstrucao = $this->connect->prepare($query_instrucao_list);
        $resultInstrucao->bindParam(':id',$id, PDO::PARAM_INT);
        $resultInstrucao->execute();

        if(($resultInstrucao) AND ($resultInstrucao->rowCount() != 0)) {
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
                'observacao_instrucao' => $observacao_instrucao
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

    public function editarInstrucao($dados) {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_instrucao_list = "UPDATE tb_instrucoes
        SET interessado_instrucao = :interessado_instrucao, assunto_instrucao = :assunto_instrucao, executor_instrucao = :executor_instrucao,
        setor = :setor , observacao_instrucao= :observacao_instrucao WHERE id_instrucao = :id ";
        
        $editInstrucao = $this->connect->prepare($query_instrucao_list);
        $editInstrucao->bindParam(':interessado_instrucao',$dados['interessado_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':assunto_instrucao',$dados['assunto_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':executor_instrucao',$dados['executor_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':setor',$dados['setor'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':observacao_instrucao',$dados['observacao_instrucao'], PDO::PARAM_STR);
        $editInstrucao->bindParam(':id',$dados['id_instrucao']);

        $editInstrucao->execute();

        if($editInstrucao->rowCount()){
            return "Intrução Alterado";
        } else {
            return "Intrução não Editado, Favor Validar (Error -> 01B)";
        } 
    }

    public function excluirInstrucao($dados){
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_instrucao_list = "UPDATE tb_instrucoes
        SET excluido_instrucao = 1, automatico_exclusao = GETDATE() WHERE id_instrucao = :id";

        $exclusaoInstrucao = $this->connect->prepare($query_instrucao_list);
        $exclusaoInstrucao->bindParam(':id', $dados['id_instrucao']);

        $exclusaoInstrucao->execute();

        if($exclusaoInstrucao->rowCount()){
            return "Instrucao Excluída com Sucesso";
        } else {
            return "Instrucao não Excluída, Favor Validar (Error -> 01B)";
        }

    }
}