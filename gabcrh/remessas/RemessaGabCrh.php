<?php

include_once '../../conexao/Conexao.php';

class RemessaGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarRemessa($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_remessa = "INSERT INTO numeracaoGabCoordenadorCrhRemessas 
        (datElaboracao_remessa, automaticoCriacao_remessa, anoCriacao_remessa, assunto_remessa, executor_remessa, setorElaboracao_remessa, exclusao_remessa, observacao_remessa)
        VALUES
        (:datElaboracao_remessa, GETDATE(), YEAR(GETDATE()), :assunto_remessa, :executor_remessa, :setorElaboracao_remessa, 0, :observacao_remessa) ";

        $cad_remessa = $this->connect->prepare($query_remessa);

        $cad_remessa->bindParam(':datElaboracao_remessa', $dados['remessa']['datElaboracao_remessa']);
        $cad_remessa->bindParam(':assunto_remessa', $dados['remessa']['assunto_remessa']);
        $cad_remessa->bindParam(':executor_remessa', $dados['remessa']['executor_remessa']);
        $cad_remessa->bindParam(':setorElaboracao_remessa', $dados['remessa']['setorElaboracao_remessa']);
        $cad_remessa->bindParam(':observacao_remessa', $dados['remessa']['observacao_remessa']);
        $cad_remessa->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_remessa, numero_remessa
        FROM numeracaoGabCoordenadorCrhRemessas
        WHERE executor_remessa = :executor_remessa
        ORDER BY id_remessa DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_remessa', $dados['remessa']['executor_remessa']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_remessa =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_remessa);
            $remessa = [
                "erro" => false,
                "mensagem" => "remessa cadastrada!",
                'id_remessa' => $id_remessa,
                'numero_remessa' => $numero_remessa
            ];
            http_response_code(200);
            echo json_encode($remessa);
        }
    }

    public function listarRemessa()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_remessa_list = "SELECT 
        id_remessa, numero_remessa, datElaboracao_remessa, assunto_remessa, executor_remessa, setorElaboracao_remessa, exclusao_remessa, observacao_remessa,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoremessa, numeracaoDepartamento.nome_departamento as setorremessa
        FROM numeracaoGabCoordenadorCrhRemessas
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhRemessas.assunto_remessa = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhRemessas.setorElaboracao_remessa = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_remessa = 0 
        ORDER BY id_remessa DESC
        ";

        $result_list_remessa = $this->connect->prepare($query_remessa_list);
        $result_list_remessa->execute();

        if (($result_list_remessa) and ($result_list_remessa->rowCount() != 0)) {
            while ($result_remessa = $result_list_remessa->fetch(PDO::FETCH_ASSOC)) {
                extract($result_remessa);
                $lista_remessa[$id_remessa] = [
                    'id_remessa' => $id_remessa,
                    'numero_remessa' => $numero_remessa,
                    'assunto_remessa' => $assunto_remessa,
                    'assuntoremessa' => $assuntoremessa,
                    'datElaboracao_remessa' => date('d/m/Y', strtotime($datElaboracao_remessa)),
                    'executor_remessa' => $executor_remessa,
                    'setorElaboracao_remessa' => $setorElaboracao_remessa,
                    'setorremessa' => $setorremessa,
                    'observacao_remessa' => $observacao_remessa
                ];
            }
            http_response_code(200);
            echo json_encode($lista_remessa);
        }
    }

    public function visualizarRemessa($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_remessa = "SELECT
        id_remessa, numero_remessa, datElaboracao_remessa, assunto_remessa, executor_remessa, setorElaboracao_remessa, observacao_remessa,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoremessa, numeracaoDepartamento.nome_departamento as setorremessa
        FROM numeracaoGabCoordenadorCrhRemessas
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhRemessas.assunto_remessa = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhRemessas.setorElaboracao_remessa = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_remessa = 0 and id_remessa = :id";

        $result_remessa = $this->connect->prepare($query_visualizar_remessa);
        $result_remessa->bindParam(':id', $id, PDO::PARAM_INT);
        $result_remessa->execute();

        if (($result_remessa) and ($result_remessa->rowCount() != 0)) {
            $row_remessa = $result_remessa->fetch(PDO::FETCH_ASSOC);
            extract($row_remessa);
            $remessa = [
                'id_remessa' => $id_remessa,
                'numero_remessa' => $numero_remessa,
                'assunto_remessa' => $assunto_remessa,
                'assuntoremessa' => $assuntoremessa,
                'datElaboracao_remessa' => date('d/m/Y', strtotime($datElaboracao_remessa)),
                'executor_remessa' => $executor_remessa,
                'setorElaboracao_remessa' => $setorElaboracao_remessa,
                'setorremessa' => $setorremessa,
                'observacao_remessa' => $observacao_remessa
            ];

            $response = [
                "erro" => false,
                "remessa" => $remessa
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "remessa não encontrada!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarRemessa($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editarremessa = "UPDATE numeracaoGabCoordenadorCrhRemessas
        SET assunto_remessa = :assunto_remessa , executor_remessa = :executor_remessa, setorElaboracao_remessa = :setorElaboracao_remessa, 
        observacao_remessa = :observacao_remessa 
        WHERE id_remessa = :id_remessa ";

        $editremessa = $this->connect->prepare($query_editarremessa);
        $editremessa->bindParam(':assunto_remessa', $dados['assunto_remessa'], PDO::PARAM_INT);
        $editremessa->bindParam(':executor_remessa', $dados['executor_remessa'], PDO::PARAM_STR);
        $editremessa->bindParam(':setorElaboracao_remessa', $dados['setorElaboracao_remessa'], PDO::PARAM_INT);
        $editremessa->bindParam(':observacao_remessa', $dados['observacao_remessa'], PDO::PARAM_STR);
        $editremessa->bindParam(':id_remessa', $dados['id_remessa'], PDO::PARAM_INT);

        $editremessa->execute();
        
    }

    public function newListarRemessa($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_remessa = $this->listarRemessa();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_remessa, numero_remessa, datElaboracao_remessa, assunto_remessa, executor_remessa, setorElaboracao_remessa, observacao_remessa,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntoremessa, numeracaoDepartamento.nome_departamento as setorremessa
            FROM numeracaoGabCoordenadorCrhRemessas
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhRemessas.assunto_remessa = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhRemessas.setorElaboracao_remessa = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_remessa = 0  and numero_remessa like :numero_remessa OR
            exclusao_remessa = 0  and executor_remessa like :executor_remessa OR
            exclusao_remessa = 0  and assunto_remessa like :assunto_remessa OR  
            exclusao_remessa = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntoremessa OR
            exclusao_remessa = 0  and numeracaoDepartamento.nome_departamento LIKE :setorremessa
            ORDER BY id_remessa DESC";

            $listar_remessa = $this->connect->prepare($newListar);
            $listar_remessa->bindParam(':numero_remessa', $ParLike, PDO::PARAM_INT);
            $listar_remessa->bindParam(':executor_remessa', $ParLike, PDO::PARAM_STR);
            $listar_remessa->bindParam(':assunto_remessa', $ParLike, PDO::PARAM_INT);
            $listar_remessa->bindParam(':assuntoremessa', $ParLike, PDO::PARAM_STR);
            $listar_remessa->bindParam(':setorremessa', $ParLike, PDO::PARAM_STR);
            $listar_remessa->execute();

            if (($listar_remessa) and ($listar_remessa->rowCount() != 0)) {
                while ($result_remessa = $listar_remessa->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_remessa);
                    $lista_remessa[$id_remessa] = [
                        'id_remessa' => $id_remessa,
                        'numero_remessa' => $numero_remessa,
                        'assunto_remessa' => $assunto_remessa,
                        'assuntoremessa' => $assuntoremessa,
                        'datElaboracao_remessa' => date('d/m/Y', strtotime($datElaboracao_remessa)),
                        'executor_remessa' => $executor_remessa,
                        'setorElaboracao_remessa' => $setorElaboracao_remessa,
                        'setorremessa' => $setorremessa,
                        'observacao_remessa' => $observacao_remessa
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_remessa);
            }
        }
    }

    public function excluirRemessa($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_remessa_list = "UPDATE numeracaoGabCoordenadorCrhRemessas
        SET automaticoExclusao_remessa = GETDATE(), exclusao_remessa = 1 
        WHERE id_remessa= :id";

        $exclusaoremessa = $this->connect->prepare($query_remessa_list);
        $exclusaoremessa->bindParam(':id', $dados['id_remessa']);
        $exclusaoremessa->execute();
    }
}