<?php

include_once '../../conexao/Conexao.php';
include '../../validacao/Validacao.php';

class ComunicadosGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarComunicados($dados)
    {

        $validacaoComunicado = new Validacao();
        $validacaoComunicado->validacaoComunicado($dados);

        if ($validacaoComunicado == true) {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_cadComunicadosGabCrh = "INSERT INTO numeracaoGabCoordenadorCrhComunicado 
        (datEmissao_comunicado, automaticoCriacao_comunicado, anoCriacao_comunicado, assunto_comunicado, executor_comunicado, setorElaboracao_comunicado, exclusao_comunicado, observacao_comunicado)
        VALUES
        (:datEmissao_comunicado, GETDATE(), YEAR(GETDATE()), :assunto_comunicado, :executor_comunicado, :setorElaboracao_comunicado, 0, :observacao_comunicado) ";

        $cad_comunicados = $this->connect->prepare($query_cadComunicadosGabCrh);
       
        $cad_comunicados->bindParam(':datEmissao_comunicado', $dados['cadComunicadosGabCrh']['datEmissao_comunicado']);
        $cad_comunicados->bindParam(':assunto_comunicado', $dados['cadComunicadosGabCrh']['assunto_comunicado']);
        $cad_comunicados->bindParam(':executor_comunicado', $dados['cadComunicadosGabCrh']['executor_comunicado']);
        $cad_comunicados->bindParam(':setorElaboracao_comunicado', $dados['cadComunicadosGabCrh']['setorElaboracao_comunicado']);
        $cad_comunicados->bindParam(':observacao_comunicado', $dados['cadComunicadosGabCrh']['observacao_comunicado']);

        $response = [
            "erro" => false,
            "mensagem" => "Comunicado caDastrado!"  
        ];

        } else {

            $response = [
                "erro" => true,
                "mensagem" => "Comunicado não cadastrado!"  
            ];
        }
        echo json_encode($response);

        


        
        
    }
}
