<?php

include_once '../../conexao/Conexao.php';

class AssuntosGabCrh extends Conexao {

    protected $connect; 
    protected $dados;

    public function visualizarAssuntoGabCoordenadorCrh(){
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_list_assunto = "SELECT id_assunto, assunto FROM numeracaoGabCoordenadorCrhAssuntos";
        $result_list_assunto = $this->connect->prepare($query_list_assunto);
        $result_list_assunto->execute();

        if(($result_list_assunto)AND ($result_list_assunto->rowCount() !=0)){
            while($resultadoAssunto = $result_list_assunto->fetch(PDO::FETCH_ASSOC)){
                extract($resultadoAssunto);
                $listAssuntoGabCoordenadorCrh['registro_assunto'][$id_assunto] = [
                    'id_assunto' => $id_assunto,
                    'assunto' => $assunto
                ];
            }
        }
        http_response_code(200);
        echo json_encode($listAssuntoGabCoordenadorCrh);
    }

}