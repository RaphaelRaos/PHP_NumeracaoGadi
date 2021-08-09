<?php


    include_once '../conexao/Conexao.php';

class RelacaoRemessa extends Conexao {

    protected object $connect;
    protected $dados;

    public function cadastrarRemessa($dados){

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadRemessa = "INSERT INTO tb_relRemessas 
        (        
        numProcesso_remessa, des_ua ,  des_uo ,interessado_remessa ,assunto_remessa ,datEmissao_remessa ,automatico_entrada ,executor_remessa ,
        area_remessa ,observacao_remessa ,excluido_remessa
        )
        VALUES
        (
        :numProcesso_remessa, :des_ua ,  :des_uo , :interessado_remessa , :assunto_remessa , :datEmissao_remessa , GETDATE() ,:executor_remessa ,
        :area_remessa , :observacao_remessa, 0
        )";

        $cadRemessa = $this->connect->prepare($query_cadRemessa);
        $cadRemessa->bindParam(':numProcesso_remessa',$dados['remessa']['numProcesso_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':des_ua',$dados['remessa']['des_ua'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':des_uo',$dados['remessa']['des_uo'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':interessado_remessa',$dados['remessa']['interessado_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':assunto_remessa',$dados['remessa']['assunto_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':datEmissao_remessa',$dados['remessa']['datEmissao_remessa']);
        $cadRemessa->bindParam(':executor_remessa',$dados['remessa']['executor_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':area_remessa',$dados['remessa']['area_remessa'], PDO::PARAM_STR);
        $cadRemessa->bindParam(':observacao_remessa',$dados['remessa']['observacao_remessa'], PDO::PARAM_STR);
        
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
        id_remessa, numero_remessa, numProcesso_remessa, des_ua ,  des_uo ,interessado_remessa ,assunto_remessa ,datEmissao_remessa ,executor_remessa ,
        area_remessa ,observacao_remessa from tb_relRemessas where excluido_remessa = 0 ";

        $result_listRemessa = $this->connect->prepare ($query_listRemessa);
        $result_listRemessa->execute();

        if (($result_listRemessa) AND ($result_listRemessa->rowCount() !=0)) {
            while ($resultRemessa = $result_listRemessa->fetch(PDO::FETCH_ASSOC)){
                extract ($resultRemessa);

                $listaRemessa ['registro_remessa'][$id_remessa] = [
                  'id_remessa' => $id_remessa,
                  'numero_remessa' => $numero_remessa,
                  'numProcesso_remessa' => $numProcesso_remessa,
                  'des_ua' => $des_ua,
                  'des_uo' => $des_uo,
                  'interessado_remessa' => $interessado_remessa,
                  'assunto_remessa' => $assunto_remessa,
                  'datEmissao_remessa' => $datEmissao_remessa,
                  'executor_remessa' => $executor_remessa,
                  'area_remessa' => $area_remessa,
                  'observacao_remessa' => $observacao_remessa,
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
        id_remessa, numero_remessa, numProcesso_remessa, des_ua ,  des_uo ,interessado_remessa ,assunto_remessa ,datEmissao_remessa ,executor_remessa ,
        area_remessa ,observacao_remessa from tb_relRemessas where excluido_remessa = 0 AND id_remessa = :id";

        $result_listRemessa = $this->connect->prepare ($query_listRemessa);
        $result_listRemessa->bindParam(':id', $id, PDO::PARAM_INT);
        $result_listRemessa->execute();

        if (($result_listRemessa) AND ($result_listRemessa->rowCount() !=0)) {
            $row_remessa = $result_listRemessa->fetch(PDO::FETCH_ASSOC);
                extract ($row_remessa);

                $listaRemessa ['registro_remessa'][$id_remessa] = [
                  'id_remessa' => $id_remessa,
                  'numero_remessa' => $numero_remessa,
                  'numProcesso_remessa' => $numProcesso_remessa,
                  'des_ua' => $des_ua,
                  'des_uo' => $des_uo,
                  'interessado_remessa' => $interessado_remessa,
                  'assunto_remessa' => $assunto_remessa,
                  'datEmissao_remessa' => $datEmissao_remessa,
                  'executor_remessa' => $executor_remessa,
                  'area_remessa' => $area_remessa,
                  'observacao_remessa' => $observacao_remessa,
                ];
                $response = [
                    "erro" => false,
                    "mensagem" => $listaRemessa
                ];
            } else {
                $response = [
                    "erro" => true,
                    "mensagem" => "OFÍCIO NÃO ENCONTRADO, FAVOR VALIDAR AS INFORMAÇÕES (ERRO 1-B)"
                ];
            }       
            http_response_code(200);
            echo json_encode(($response));
    }

    public function editarRemessa($dados) {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editRemessa = "UPDATE tb_relRemessas SET         
        numProcesso_remessa = :numProcesso_remessa  , des_ua = :des_ua ,  des_uo = :des_uo , interessado_remessa = :interessado_remessa, assunto_remessa = :assunto_remessa,
        executor_remessa = :executor_remessa, area_remessa = :area_remessa  ,observacao_remessa = :observacao_remessa WHERE excluido_remessa = 0 AND id_remessa = :id";
        
        $editRemessa = $this->connect->prepare($query_editRemessa);
        $editRemessa->bindParam(':numProcesso_remessa',$dados['numProcesso_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':des_ua',$dados['des_ua'], PDO::PARAM_STR);
        $editRemessa->bindParam(':des_uo',$dados['des_uo'], PDO::PARAM_STR);
        $editRemessa->bindParam(':interessado_remessa',$dados['interessado_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':assunto_remessa',$dados['assunto_remessa'], PDO::PARAM_STR);        
        $editRemessa->bindParam(':executor_remessa',$dados['executor_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':area_remessa',$dados['area_remessa'], PDO::PARAM_STR);
        $editRemessa->bindParam(':observacao_remessa',$dados['observacao_remessa'], PDO::PARAM_STR);
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

        $query_excluirRemessa = "UPDATE tb_relRemessas SET         
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
}
