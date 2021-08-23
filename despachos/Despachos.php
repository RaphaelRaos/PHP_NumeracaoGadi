<?php

include_once '../conexao/Conexao.php';

class Despachos extends Conexao {

    public object $connect;
    public $dados;

    public function cadastrar($dados) {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadDespachos = "INSERT INTO tb_despachos (
        numero_sisrad_processo, des_ua, des_ugo, interessado_despacho, assunto_despacho,
        datEntrada_despacho, automatico_entrada, executor_despacho, setor,
        observacao_despacho, excluido_despacho)
        VALUES
        (:numero_sisrad_processo, :des_ua, :des_ugo, :interessado_despacho, :assunto_despacho,
        :datEntrada_despacho, GETDATE(), :executor_despacho, :setor,
         :observacao_despacho, 0)";

         $cad_despachos = $this->connect->prepare($query_cadDespachos);
         $cad_despachos->bindParam(':numero_sisrad_processo', $dados['despacho']['numero_sisrad_processo'], PDO::PARAM_STR);
         $cad_despachos->bindParam(':des_ua', $dados['despacho']['des_ua'], PDO::PARAM_STR);
         $cad_despachos->bindParam(':des_ugo', $dados['despacho']['des_ugo'], PDO::PARAM_STR);
         $cad_despachos->bindParam(':interessado_despacho', $dados['despacho']['interessado_despacho'], PDO::PARAM_STR);
         $cad_despachos->bindParam(':assunto_despacho', $dados['despacho']['assunto_despacho'], PDO::PARAM_STR);
         $cad_despachos->bindParam(':datEntrada_despacho', $dados['despacho']['datEntrada_despacho']);
         $cad_despachos->bindParam(':executor_despacho', $dados['despacho']['executor_despacho']);
         $cad_despachos->bindParam(':setor', $dados['despacho']['setor'], PDO::PARAM_STR);
         $cad_despachos->bindParam(':observacao_despacho', $dados['despacho']['observacao_despacho'], PDO::PARAM_STR);
         
         $cad_despachos->execute();

         if($cad_despachos ->rowCount()){
             return "CADASTRO REALIZADO COM SUCESSO";
         } else {
             return "CADASTRO NÃO REALIZADO. POR FAVOR, TENTE NOVAMENTE (Erro 1-B)";
         }
    }

    public function listar() {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "SELECT id_despacho, numero_despacho, numero_sisrad_processo, des_ua, des_ugo, interessado_despacho, assunto_despacho,
        datEntrada_despacho, executor_despacho,setor, observacao_despacho FROM tb_despachos WHERE excluido_despacho = 0 AND datSaida_despacho IS NULL  ORDER BY id_despacho DESC "; 
        
        $result_list_despachos = $this->connect->prepare($query_despacho_list);
        $result_list_despachos->execute();

        if (($result_list_despachos) AND ($result_list_despachos->rowCount() !=0)){
            while ($result_despachos = $result_list_despachos->fetch(PDO::FETCH_ASSOC)){
                extract ($result_despachos);
                $lista_despachos['registros_despachos'][$id_despacho] = [
                    'id_despacho' => $id_despacho,
                    'numero_despacho' => $numero_despacho,
                    'numero_sisrad_processo' => $numero_sisrad_processo,
                    'des_ua' => $des_ua,
                    'des_ugo' => $des_ugo,
                    'interessado_despacho'=> $interessado_despacho,
                    'assunto_despacho' => $assunto_despacho,
                    'datEntrada_despacho' => $datEntrada_despacho,
                    'executor_despacho' => $executor_despacho,
                    'setor' => $setor,
                    'observacao_despacho' => $observacao_despacho
                ];
            }
            http_response_code(200);
            echo json_encode($lista_despachos);
        }
    }

    public function visualizar($id) {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "SELECT id_despacho, numero_despacho, numero_sisrad_processo, des_ua, des_ugo, interessado_despacho, assunto_despacho,
        datEntrada_despacho, datSaida_despacho, executor_despacho,setor, observacao_despacho FROM tb_despachos WHERE excluido_despacho = 0 AND datSaida_despacho IS NULL AND id_despacho = :id" ;

        $result_despacho = $this->connect->prepare($query_despacho_list);
        $result_despacho->bindParam(':id', $id, PDO::PARAM_INT);
        $result_despacho->execute();

        if(($result_despacho)AND ($result_despacho->rowCount() != 0 )){
            $row_despacho = $result_despacho->fetch(PDO::FETCH_ASSOC);
            extract($row_despacho);

            $despacho = [
                    'id_despacho' => $id_despacho,
                    'numero_despacho' => $numero_despacho,
                    'numero_sisrad_processo' => $numero_sisrad_processo,
                    'des_ua' => $des_ua,
                    'des_ugo' => $des_ugo,
                    'interessado_despacho'=> $interessado_despacho,
                    'assunto_despacho' => $assunto_despacho,
                    'datEntrada_despacho' => $datEntrada_despacho,
                    'executor_despacho' => $executor_despacho,
                    'setor' => $setor,
                    'observacao_despacho' => $observacao_despacho
            ];
            $response = [
                "erro" => false,
                "despacho" => $despacho
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "Despacho não encontrado!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarDespacho($dados) {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "UPDATE tb_despachos
        SET numero_sisrad_processo = :numero_sisrad_processo, des_ua = :des_ua, des_ugo = :des_ugo,
        interessado_despacho = :interessado_despacho, assunto_despacho = :assunto_despacho , executor_despacho = :executor_despacho,
        setor = :setor, observacao_despacho = :observacao_despacho WHERE id_despacho = :id AND excluido_despacho = 0"; 

        $editDespacho = $this->connect->prepare($query_despacho_list);
        $editDespacho->bindParam(':numero_sisrad_processo',$dados['numero_sisrad_processo'], PDO::PARAM_STR);
        $editDespacho->bindParam(':des_ua',$dados['des_ua'], PDO::PARAM_STR);
        $editDespacho->bindParam(':des_ugo',$dados['des_ugo'], PDO::PARAM_STR);
        $editDespacho->bindParam(':interessado_despacho',$dados['interessado_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':assunto_despacho',$dados['assunto_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':executor_despacho',$dados['executor_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':setor',$dados['setor'], PDO::PARAM_STR);
        $editDespacho->bindParam(':observacao_despacho',$dados['observacao_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':id',$dados['id_despacho']);

        $editDespacho->execute();

        if($editDespacho->rowCount()){
            return "Despacho Alterado";
        } else {
            return "Despacho não Editado, Favor Validar (Error -> 01B)";
        }        
    }

    public function excluirDespacho($dados){

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "UPDATE tb_despachos 
        SET excluido_despacho = 1, automatico_exclusao = GETDATE() WHERE id_despacho = :id"; 

        $exclusaoDespacho = $this->connect->prepare($query_despacho_list);
        $exclusaoDespacho->bindParam(':id', $dados['id_despacho']);

        $exclusaoDespacho->execute();

        if($exclusaoDespacho->rowCount()){
            return "Despacho Excluído com Sucesso";
        } else {
            return "Despacho não Excluído, Favor Validar (Error -> 01B)";
        } 
    }

    public function saidaDespacho($dados){

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_despacho_list = "UPDATE tb_despachos 
        SET datSaida_despacho =  :datSaida_despacho, automatico_saida = GETDATE() WHERE id_despacho = :id"; 

        $saidaDespacho = $this->connect->prepare($query_despacho_list);
        $saidaDespacho->bindParam(':datSaida_despacho', $dados['datSaida_despacho']);
        $saidaDespacho->bindParam(':id', $dados['id_despacho']);

        $saidaDespacho->execute();

        if($saidaDespacho->rowCount()){
            return "Saída Realizada com Sucesso";
        } else {
            return "Saída não realizada, Favor Validar (Error -> 01B)";
        }
    } 

    public function newListarDespachos($BuscaFinal) {
        if($BuscaFinal == null){
            $BFetchFull = $this->listar();

        } else {
            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%'.$BuscaFinal.'%';

            $BFetch = "SELECT id_despacho, numero_despacho, numero_sisrad_processo, des_ua, des_ugo, interessado_despacho, assunto_despacho,
            datEntrada_despacho, executor_despacho,setor, observacao_despacho FROM tb_despachos
            WHERE excluido_despacho = 0 AND datSaida_despacho IS NULL AND numero_despacho LIKE :numero_despacho OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND numero_sisrad_processo LIKE :numero_sisrad_processo OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND des_ua LIKE :des_ua OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND interessado_despacho LIKE :interessado_despacho OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND assunto_despacho LIKE :assunto_despacho OR
            excluido_despacho = 0 AND datSaida_despacho IS NULL AND executor_despacho LIKE :executor_despacho 
            ORDER BY id_despacho DESC ";

            $BFetchFull = $this->connect->prepare($BFetch);

            $BFetchFull->bindParam(':numero_despacho', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':numero_sisrad_processo', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':des_ua', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':interessado_despacho', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':assunto_despacho', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':executor_despacho', $ParLike, PDO::PARAM_STR);
            $BFetchFull->execute();

            $I = 0;

            if(($BFetchFull) AND ($BFetchFull->rowCount() !=0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {;
                extract($Fetch);           
                            
                    $listaDespacho[$I] = [
                    'id_despacho' =>$Fetch['id_despacho'],
                    'numero_despacho' =>$Fetch['numero_despacho'],
                    'numero_sisrad_processo' =>$Fetch['numero_sisrad_processo'],
                    'des_ua' =>$Fetch['des_ua'],
                    'des_ugo' =>$Fetch['des_ugo'],
                    'interessado_despacho'=>$Fetch['interessado_despacho'],
                    'assunto_despacho' =>$Fetch['assunto_despacho'],
                    'datEntrada_despacho' =>$Fetch['datEntrada_despacho'],
                    'executor_despacho' =>$Fetch['executor_despacho'],
                    'setor' =>$Fetch['setor'],
                    'observacao_despacho' =>$Fetch['observacao_despacho']
                    ];
                    $I++;
                }
                http_response_code(200);
                echo json_encode($listaDespacho);
            }
        }
    }
}

