<?php

include_once '../../conexao/Conexao.php';

class DespachosGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarDespachos($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_Despachos = "INSERT INTO numeracaoGabCoordenadorCrhDespachos 
        (datElaboracao_despacho, automaticoCriacao_despacho, anoCriacao_despacho, assunto_despacho, executor_despacho, setorElaboracao_despacho, exclusao_despacho, observacao_despacho)
        VALUES
        (:datElaboracao_despacho, GETDATE(), YEAR(GETDATE()), :assunto_despacho, :executor_despacho, :setorElaboracao_despacho, 0, :observacao_despacho) ";

        $cad_despachos = $this->connect->prepare($query_Despachos);

        $cad_despachos->bindParam(':datElaboracao_despacho', $dados['despacho']['datElaboracao_despacho']);
        $cad_despachos->bindParam(':assunto_despacho', $dados['despacho']['assunto_despacho']);
        $cad_despachos->bindParam(':executor_despacho', $dados['despacho']['executor_despacho']);
        $cad_despachos->bindParam(':setorElaboracao_despacho', $dados['despacho']['setorElaboracao_despacho']);
        $cad_despachos->bindParam(':observacao_despacho', $dados['despacho']['observacao_despacho']);
        $cad_despachos->execute();

        $query_retornoDespacho = "SELECT TOP 1 id_despacho, numero_despacho
        FROM numeracaoGabCoordenadorCrhDespachos
        WHERE executor_despacho = :executor_despacho
        ORDER BY id_despacho DESC";

        $retornoDespacho = $this->connect->prepare($query_retornoDespacho);
        $retornoDespacho->bindParam(':executor_despacho', $dados['despacho']['executor_despacho']);
        $retornoDespacho->execute();
        if (($retornoDespacho) and ($retornoDespacho->rowCount() != 0)) {
            $row_despacho =  $retornoDespacho->fetch(PDO::FETCH_ASSOC);
            extract($row_despacho);
            $despacho = [
                "erro" => false,
                "mensagem" => "despacho cadastrado!",
                'id_despacho' => $id_despacho,
                'numero_despacho' => $numero_despacho
            ];
            http_response_code(200);
            echo json_encode($despacho);
        }
    }

    public function listarDespachos()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_Despachos_list = "SELECT 
        id_despacho, numero_despacho, datElaboracao_despacho, assunto_despacho, executor_despacho, setorElaboracao_despacho, exclusao_despacho, observacao_despacho,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntodespacho, numeracaoDepartamento.nome_departamento as setordespacho
        FROM numeracaoGabCoordenadorCrhDespachos
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhDespachos.assunto_despacho = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhDespachos.setorElaboracao_despacho = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_despacho = 0 
        ORDER BY id_despacho DESC
        ";

        $result_list_Despachos = $this->connect->prepare($query_Despachos_list);
        $result_list_Despachos->execute();

        if (($result_list_Despachos) and ($result_list_Despachos->rowCount() != 0)) {
            while ($result_Despachos = $result_list_Despachos->fetch(PDO::FETCH_ASSOC)) {
                extract($result_Despachos);
                $lista_despacho[$id_despacho] = [
                    'id_despacho' => $id_despacho,
                    'numero_despacho' => $numero_despacho,
                    'assunto_despacho' => $assunto_despacho,
                    'assuntodespacho' => $assuntodespacho,
                    'datElaboracao_despacho' => $datElaboracao_despacho,
                    'executor_despacho' => $executor_despacho,
                    'setorElaboracao_despacho' => $setorElaboracao_despacho,
                    'setordespacho' => $setordespacho,
                    'observacao_despacho' => $observacao_despacho
                ];
            }
            http_response_code(200);
            echo json_encode($lista_despacho);
        }
    }

    public function visualizarDespachos($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_despacho = "SELECT
        id_despacho, numero_despacho, datElaboracao_despacho, assunto_despacho, executor_despacho, setorElaboracao_despacho, observacao_despacho,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntodespacho, numeracaoDepartamento.nome_departamento as setordespacho
        FROM numeracaoGabCoordenadorCrhDespachos
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhDespachos.assunto_despacho = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhDespachos.setorElaboracao_despacho = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_despacho = 0 and id_despacho = :id";

        $result_despacho = $this->connect->prepare($query_visualizar_despacho);
        $result_despacho->bindParam(':id', $id, PDO::PARAM_INT);
        $result_despacho->execute();

        if (($result_despacho) and ($result_despacho->rowCount() != 0)) {
            $row_despacho = $result_despacho->fetch(PDO::FETCH_ASSOC);
            extract($row_despacho);
            $despacho = [
                'id_despacho' => $id_despacho,
                'numero_despacho' => $numero_despacho,
                'assunto_despacho' => $assunto_despacho,
                'assuntodespacho' => $assuntodespacho,
                'datElaboracao_despacho' => date('d/m/Y',strtotime($datElaboracao_despacho)),
                'executor_despacho' => $executor_despacho,
                'setorElaboracao_despacho' => $setorElaboracao_despacho,
                'setordespacho' => $setordespacho,
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

    public function editarDespachos($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarDespachos = "UPDATE numeracaoGabCoordenadorCrhDespachos
        SET assunto_despacho = :assunto_despacho , executor_despacho = :executor_despacho, setorElaboracao_despacho = :setorElaboracao_despacho, 
        observacao_despacho = :observacao_despacho 
        WHERE id_despacho = :id_despacho ";

        $editDespacho = $this->connect->prepare($query_editarDespachos);
        $editDespacho->bindParam(':assunto_despacho', $dados['assunto_despacho'], PDO::PARAM_INT);
        $editDespacho->bindParam(':executor_despacho', $dados['executor_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':setorElaboracao_despacho', $dados['setorElaboracao_despacho'], PDO::PARAM_INT);
        $editDespacho->bindParam(':observacao_despacho', $dados['observacao_despacho'], PDO::PARAM_STR);
        $editDespacho->bindParam(':id_despacho', $dados['id_despacho'], PDO::PARAM_INT);

        $editDespacho->execute();

        if ($editDespacho->rowCount()) {
            return "despacho Alterado";
        } else {
            return "despacho não Editado, Favor Validar (Erro -> 01B)";
        }
    }

    public function newListarDespachos($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_Despachos = $this->listarDespachos();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_despacho, numero_despacho, datElaboracao_despacho, assunto_despacho, executor_despacho, setorElaboracao_despacho, observacao_despacho,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntodespacho, numeracaoDepartamento.nome_departamento as setordespacho
            FROM numeracaoGabCoordenadorCrhDespachos
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhDespachos.assunto_despacho = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhDespachos.setorElaboracao_despacho = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_despacho = 0  and numero_despacho like :numero_despacho OR
            exclusao_despacho = 0  and executor_despacho like :executor_despacho OR
            exclusao_despacho = 0  and assunto_despacho like :assunto_despacho OR  
            exclusao_despacho = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntodespacho OR
            exclusao_despacho = 0  and numeracaoDepartamento.nome_departamento LIKE :setordespacho
            ORDER BY id_despacho DESC";

            $listar_Despachos = $this->connect->prepare($newListar);
            $listar_Despachos->bindParam(':numero_despacho', $ParLike, PDO::PARAM_INT);
            $listar_Despachos->bindParam(':executor_despacho', $ParLike, PDO::PARAM_STR);
            $listar_Despachos->bindParam(':assunto_despacho', $ParLike, PDO::PARAM_INT);
            $listar_Despachos->bindParam(':assuntodespacho', $ParLike, PDO::PARAM_STR);
            $listar_Despachos->bindParam(':setordespacho', $ParLike, PDO::PARAM_STR);
            $listar_Despachos->execute();

            if (($listar_Despachos) and ($listar_Despachos->rowCount() != 0)) {
                while ($result_Despachos = $listar_Despachos->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_Despachos);
                    $lista_despacho[$id_despacho] = [
                        'id_despacho' => $id_despacho,
                        'numero_despacho' => $numero_despacho,
                        'assunto_despacho' => $assunto_despacho,
                        'assuntodespacho' => $assuntodespacho,
                        'datElaboracao_despacho' => $datElaboracao_despacho,
                        'executor_despacho' => $executor_despacho,
                        'setorElaboracao_despacho' => $setorElaboracao_despacho,
                        'setordespacho' => $setordespacho,
                        'observacao_despacho' => $observacao_despacho
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_despacho);
            }
        }
    }

    public function excluirDespachos($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_Despachos_list = "UPDATE numeracaoGabCoordenadorCrhDespachos
        SET automaticoExclusao_despacho = GETDATE(), exclusao_despacho = 1 
        WHERE id_despacho= :id";

        $exclusaodespacho = $this->connect->prepare($query_Despachos_list);
        $exclusaodespacho->bindParam(':id', $dados['id_despacho']);
        $exclusaodespacho->execute();
    }
}
