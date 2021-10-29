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

        $query_retornoCadastro = "SELECT TOP 1 id_comunicado, numero_comunicado
        FROM numeracaoGabCoordenadorCrhComunicado
        WHERE executor_comunicado = :executor_comunicado
        ORDER BY id_comunicado DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_comunicado', $dados['comunicados']['executor_comunicado']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_comunicado =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_comunicado);
            $comunicado = [
                "erro" => false,
                "mensagem" => "Comunicado cadastrado!",
                'id_comunicado' => $id_comunicado,
                'numero_comunicado' => $numero_comunicado
            ];
            http_response_code(200);
            echo json_encode($comunicado);
        }
    }

    public function listarComunicados()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_comunicados_list = "SELECT 
        id_comunicado, numero_comunicado, datEmissao_comunicado, assunto_comunicado, executor_comunicado, setorElaboracao_comunicado, exclusao_comunicado, observacao_comunicado,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoComunicado, numeracaoDepartamento.nome_departamento as setorComunicado
        FROM numeracaoGabCoordenadorCrhComunicado
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhComunicado.assunto_comunicado = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhComunicado.setorElaboracao_comunicado = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_comunicado = 0 
        ORDER BY id_comunicado DESC
        ";

        $result_list_comunicados = $this->connect->prepare($query_comunicados_list);
        $result_list_comunicados->execute();

        if (($result_list_comunicados) and ($result_list_comunicados->rowCount() != 0)) {
            while ($result_comunicados = $result_list_comunicados->fetch(PDO::FETCH_ASSOC)) {
                extract($result_comunicados);
                $lista_comunicado[$id_comunicado] = [
                    'id_comunicado' => $id_comunicado,
                    'numero_comunicado' => $numero_comunicado,
                    'assunto_comunicado' => $assunto_comunicado,
                    'assuntoComunicado' => $assuntoComunicado,
                    'datEmissao_comunicado' => $datEmissao_comunicado,
                    'executor_comunicado' => $executor_comunicado,
                    'setorElaboracao_comunicado' => $setorElaboracao_comunicado,
                    'setorComunicado' => $setorComunicado,
                    'observacao_comunicado' => $observacao_comunicado
                ];
            }
            http_response_code(200);
            echo json_encode($lista_comunicado);
        }
    }

    public function visualizarComunicados($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_comunicado = "SELECT
        id_comunicado, numero_comunicado, datEmissao_comunicado, assunto_comunicado, executor_comunicado, setorElaboracao_comunicado, observacao_comunicado,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoComunicado, numeracaoDepartamento.nome_departamento as setorComunicado
        FROM numeracaoGabCoordenadorCrhComunicado
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhComunicado.assunto_comunicado = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhComunicado.setorElaboracao_comunicado = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_comunicado = 0 and id_comunicado = :id";

        $result_comunicado = $this->connect->prepare($query_visualizar_comunicado);
        $result_comunicado->bindParam(':id', $id, PDO::PARAM_INT);
        $result_comunicado->execute();

        if (($result_comunicado) and ($result_comunicado->rowCount() != 0)) {
            $row_comunicado = $result_comunicado->fetch(PDO::FETCH_ASSOC);
            extract($row_comunicado);
            $comunicado = [
                'id_comunicado' => $id_comunicado,
                'numero_comunicado' => $numero_comunicado,
                'assunto_comunicado' => $assunto_comunicado,
                'assuntoComunicado' => $assuntoComunicado,
                'datEmissAo_comunicado' => date('d/m/Y',strtotime($datEmissao_comunicado)),
                'executor_comunicado' => $executor_comunicado,
                'setorElaboracao_comunicado' => $setorElaboracao_comunicado,
                'setorComunicado' => $setorComunicado,
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

    public function editarComunicados($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarComunicados = "UPDATE numeracaoGabCoordenadorCrhComunicado
        SET assunto_comunicado = :assunto_comunicado , executor_comunicado = :executor_comunicado, setorElaboracao_comunicado = :setorElaboracao_comunicado, 
        observacao_comunicado = :observacao_comunicado 
        WHERE id_comunicado = :id_comunicado ";

        $editComunicado = $this->connect->prepare($query_editarComunicados);
        $editComunicado->bindParam(':assunto_comunicado', $dados['assunto_comunicado'], PDO::PARAM_INT);
        $editComunicado->bindParam(':executor_comunicado', $dados['executor_comunicado'], PDO::PARAM_STR);
        $editComunicado->bindParam(':setorElaboracao_comunicado', $dados['setorElaboracao_comunicado'], PDO::PARAM_INT);
        $editComunicado->bindParam(':observacao_comunicado', $dados['observacao_comunicado'], PDO::PARAM_STR);
        $editComunicado->bindParam(':id_comunicado', $dados['id_comunicado'], PDO::PARAM_INT);

        $editComunicado->execute();

        if ($editComunicado->rowCount()) {
            return "Comunicado Alterado";
        } else {
            return "Comunicado não Editado, Favor Validar (Erro -> 01B)";
        }
    }

    public function newListarComunicados($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_comunicados = $this->listarComunicados();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_comunicado, numero_comunicado, datEmissao_comunicado, assunto_comunicado, executor_comunicado, setorElaboracao_comunicado, observacao_comunicado,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoComunicado, numeracaoDepartamento.nome_departamento as setorComunicado
            FROM numeracaoGabCoordenadorCrhComunicado
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhComunicado.assunto_comunicado = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhComunicado.setorElaboracao_comunicado = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_comunicado = 0  and numero_comunicado like :numero_comunicado OR
            exclusao_comunicado = 0  and executor_comunicado like :executor_comunicado OR
            exclusao_comunicado = 0  and assunto_comunicado like :assunto_comunicado OR  
            exclusao_comunicado = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntoComunicado OR
            exclusao_comunicado = 0  and numeracaoDepartamento.nome_departamento LIKE :setorComunicado
            ORDER BY id_comunicado DESC";

            $listar_comunicados = $this->connect->prepare($newListar);
            $listar_comunicados->bindParam(':numero_comunicado', $ParLike, PDO::PARAM_INT);
            $listar_comunicados->bindParam(':executor_comunicado', $ParLike, PDO::PARAM_STR);
            $listar_comunicados->bindParam(':assunto_comunicado', $ParLike, PDO::PARAM_INT);
            $listar_comunicados->bindParam(':assuntoComunicado', $ParLike, PDO::PARAM_STR);
            $listar_comunicados->bindParam(':setorComunicado', $ParLike, PDO::PARAM_STR);
            $listar_comunicados->execute();

            if (($listar_comunicados) and ($listar_comunicados->rowCount() != 0)) {
                while ($result_comunicados = $listar_comunicados->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_comunicados);
                    $lista_comunicado[$id_comunicado] = [
                        'id_comunicado' => $id_comunicado,
                        'numero_comunicado' => $numero_comunicado,
                        'assunto_comunicado' => $assunto_comunicado,
                        'assuntoComunicado' => $assuntoComunicado,
                        'datEmissao_comunicado' => $datEmissao_comunicado,
                        'executor_comunicado' => $executor_comunicado,
                        'setorElaboracao_comunicado' => $setorElaboracao_comunicado,
                        'setorComunicado' => $setorComunicado,
                        'observacao_comunicado' => $observacao_comunicado
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_comunicado);
            }
        }
    }

    public function excluirComunicados($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_comunicados_list = "UPDATE numeracaoGabCoordenadorCrhComunicado
        SET automaticoExclusao_comunicado = GETDATE(), exclusao_comunicado = 1 
        WHERE id_comunicado= :id";

        $exclusaoComunicado = $this->connect->prepare($query_comunicados_list);
        $exclusaoComunicado->bindParam(':id', $dados['id_comunicado']);
        $exclusaoComunicado->execute();
    }
}
