<?php

include_once '../../conexao/Conexao.php';

class ComunicadosGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;
        
    public function cadastrarComunicados($dados)
    {
        
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_comunicados = "INSERT INTO numeracaoGabCoordenadorCrhComunicado 
        (datEmissao_comunicado, automaticoCriacao_comunicado, anoCriacao_comunicado, assunto_comunicado, executor_comunicado, setorElaboracao_comunicado, exclusao_comunicado, observacao_comunicado)
        VALUES
        (:datEmissao_comunicado, GETDATE(), YEAR(GETDATE()), :assunto_comunicado, :executor_comunicado, :setorElaboracao_comunicado, 0, :observacao_comunicado) ";

        $cad_comunicados = $this->connect->prepare($query_comunicados);
       
        $cad_comunicados->bindParam(':datEmissao_comunicado', $dados['comunicados']['datEmissao_comunicado']);
        $cad_comunicados->bindParam(':assunto_comunicado', $dados['comunicados']['assunto_comunicado']);
        $cad_comunicados->bindParam(':executor_comunicado', $dados['comunicados']['executor_comunicado']);
        $cad_comunicados->bindParam(':setorElaboracao_comunicado', $dados['comunicados']['setorElaboracao_comunicado']);
        $cad_comunicados->bindParam(':observacao_comunicado', $dados['comunicados']['observacao_comunicado']);

        $cad_comunicados->execute();        

        if ($cad_comunicados->rowCount()){
            return "Cadastro Realizado com Sucesso";
        } else {
            return "Cadastro não Realizado";
        }

    }

    public function listarComunicados(){
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_comunicados_list = "SELECT 
        id_comunicado, numero_comunicado, datEmissao_comunicado, assunto_comunicado, executor_comunicado, setorElaboracao_comunicado, exclusao_comunicado, observacao_comunicado
        FROM numeracaoGabCoordenadorCrhComunicado
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhComunicado.assunto_comunicado = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoSetor on numeracaoGabCoordenadorCrhComunicado.setorElaboracao_comunicado = numeracaoSetor.id_setor
        WHERE exclusao_comunicado = 0 
        ORDER BY id_comunicado DESC
        ";

        $result_list_comunicados = $this->connect->prepare($query_comunicados_list);
        $result_list_comunicados->execute();

        if (($result_list_comunicados) and ($result_list_comunicados->rowCount() !=0)) {
            while($result_comunicados = $result_list_comunicados->fetch(PDO::FETCH_ASSOC)){
                extract($result_comunicados);
                $lista_comunicado[$id_comunicado] = [
                    'id_comunicado' => $id_comunicado,
                    'numero_comunicado' => $numero_comunicado,
                    'datEmissao_comunicado' => $datEmissao_comunicado,
                    'assunto_comunicado' => $assunto_comunicado,
                    'executor_comunicado' => $executor_comunicado,
                    'setorElaboracao_comunicado' => $setorElaboracao_comunicado,
                    'exclusao_comunicado' => $exclusao_comunicado,
                    'observacao_comunicado' => $observacao_comunicado,                                         
                ];
            }
            http_response_code(200);
            echo json_encode($lista_comunicado);
        }
    }
}
