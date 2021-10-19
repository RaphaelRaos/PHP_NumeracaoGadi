<?php

include_once '../../conexao/Conexao.php';

class OficioGabCrh extends Conexao
{
    protected $connect;
    protected $dados;
    protected $id;

    public function cadastrarOficio($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_oficio = "INSERT INTO numeracaoGabCoordenadorCrhOficios 
        (datElaboracao_oficio, automaticoCriacao_oficio, anoCriacao_oficio, assunto_oficio, executor_oficio, setorElaboracao_oficio, exclusao_oficio, observacao_oficio)
        VALUES
        (:datElaboracao_oficio, GETDATE(), YEAR(GETDATE()), :assunto_oficio, :executor_oficio, :setorElaboracao_oficio, 0, :observacao_oficio) ";

        $cad_oficio = $this->connect->prepare($query_oficio);

        $cad_oficio->bindParam(':datElaboracao_oficio', $dados['oficio']['datElaboracao_oficio']);
        $cad_oficio->bindParam(':assunto_oficio', $dados['oficio']['assunto_oficio']);
        $cad_oficio->bindParam(':executor_oficio', $dados['oficio']['executor_oficio']);
        $cad_oficio->bindParam(':setorElaboracao_oficio', $dados['oficio']['setorElaboracao_oficio']);
        $cad_oficio->bindParam(':observacao_oficio', $dados['oficio']['observacao_oficio']);
        $cad_oficio->execute();

        $query_retornoCadastro = "SELECT TOP 1 id_oficio, numero_oficio
        FROM numeracaoGabCoordenadorCrhOficios
        WHERE executor_oficio = :executor_oficio
        ORDER BY id_oficio DESC";

        $retornoCadastro = $this->connect->prepare($query_retornoCadastro);
        $retornoCadastro->bindParam(':executor_oficio', $dados['oficio']['executor_oficio']);
        $retornoCadastro->execute();
        if (($retornoCadastro) and ($retornoCadastro->rowCount() != 0)) {
            $row_oficio =  $retornoCadastro->fetch(PDO::FETCH_ASSOC);
            extract($row_oficio);
            $oficio = [
                "erro" => false,
                "mensagem" => "oficio cadastrado!",
                'id_oficio' => $id_oficio,
                'numero_oficio' => $numero_oficio
            ];
            http_response_code(200);
            echo json_encode($oficio);
        }
    }

    public function listarOficio()
    {
        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_oficio_list = "SELECT 
        id_oficio, numero_oficio, datElaboracao_oficio, assunto_oficio, executor_oficio, setorElaboracao_oficio, exclusao_oficio, observacao_oficio,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntooficio, numeracaoDepartamento.nome_departamento as setoroficio
        FROM numeracaoGabCoordenadorCrhOficios
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhOficios.assunto_oficio = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhOficios.setorElaboracao_oficio = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_oficio = 0 
        ORDER BY id_oficio DESC
        ";

        $result_list_oficio = $this->connect->prepare($query_oficio_list);
        $result_list_oficio->execute();

        if (($result_list_oficio) and ($result_list_oficio->rowCount() != 0)) {
            while ($result_oficio = $result_list_oficio->fetch(PDO::FETCH_ASSOC)) {
                extract($result_oficio);
                $lista_oficio[$id_oficio] = [
                    'id_oficio' => $id_oficio,
                    'numero_oficio' => $numero_oficio,
                    'assunto_oficio' => $assunto_oficio,
                    'assuntooficio' => $assuntooficio,
                    'datElaboracao_oficio' => $datElaboracao_oficio,
                    'executor_oficio' => $executor_oficio,
                    'setorElaboracao_oficio' => $setorElaboracao_oficio,
                    'setoroficio' => $setoroficio,
                    'observacao_oficio' => $observacao_oficio
                ];
            }
            http_response_code(200);
            echo json_encode($lista_oficio);
        }
    }

    public function visualizarOficio($id)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $response = "";

        $query_visualizar_oficio = "SELECT
        id_oficio, numero_oficio, datElaboracao_oficio, assunto_oficio, executor_oficio, setorElaboracao_oficio, observacao_oficio,
        numeracaoGabCoordenadorCrhAssuntos.assunto as assuntooficio, numeracaoDepartamento.nome_departamento as setoroficio
        FROM numeracaoGabCoordenadorCrhOficios
        INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhOficios.assunto_oficio = numeracaoGabCoordenadorCrhAssuntos.id_assunto
        INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhOficios.setorElaboracao_oficio = numeracaoDepartamento.id_deparatamento
        WHERE exclusao_oficio = 0 and id_oficio = :id";

        $result_oficio = $this->connect->prepare($query_visualizar_oficio);
        $result_oficio->bindParam(':id', $id, PDO::PARAM_INT);
        $result_oficio->execute();

        if (($result_oficio) and ($result_oficio->rowCount() != 0)) {
            $row_oficio = $result_oficio->fetch(PDO::FETCH_ASSOC);
            extract($row_oficio);
            $oficio = [
                'id_oficio' => $id_oficio,
                'numero_oficio' => $numero_oficio,
                'assunto_oficio' => $assunto_oficio,
                'assuntooficio' => $assuntooficio,
                'datElaboracao_oficio' => $datElaboracao_oficio,
                'executor_oficio' => $executor_oficio,
                'setorElaboracao_oficio' => $setorElaboracao_oficio,
                'setoroficio' => $setoroficio,
                'observacao_oficio' => $observacao_oficio
            ];

            $response = [
                "erro" => false,
                "oficio" => $oficio
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "oficio não encontrado!"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarOficio($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_editaroficio = "UPDATE numeracaoGabCoordenadorCrhOficios
        SET assunto_oficio = :assunto_oficio , executor_oficio = :executor_oficio, setorElaboracao_oficio = :setorElaboracao_oficio, 
        observacao_oficio = :observacao_oficio 
        WHERE id_oficio = :id_oficio ";

        $editoficio = $this->connect->prepare($query_editaroficio);
        $editoficio->bindParam(':assunto_oficio', $dados['assunto_oficio'], PDO::PARAM_INT);
        $editoficio->bindParam(':executor_oficio', $dados['executor_oficio'], PDO::PARAM_STR);
        $editoficio->bindParam(':setorElaboracao_oficio', $dados['setorElaboracao_oficio'], PDO::PARAM_INT);
        $editoficio->bindParam(':observacao_oficio', $dados['observacao_oficio'], PDO::PARAM_STR);
        $editoficio->bindParam(':id_oficio', $dados['id_oficio'], PDO::PARAM_INT);

        $editoficio->execute();
        
    }

    public function newListarOficio($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $listar_oficio = $this->listarOficio();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';
            $newListar = "SELECT
            id_oficio, numero_oficio, datElaboracao_oficio, assunto_oficio, executor_oficio, setorElaboracao_oficio, observacao_oficio,
            numeracaoGabCoordenadorCrhAssuntos.assunto as assuntooficio, numeracaoDepartamento.nome_departamento as setoroficio
            FROM numeracaoGabCoordenadorCrhOficios
            INNER JOIN numeracaoGabCoordenadorCrhAssuntos ON numeracaoGabCoordenadorCrhOficios.assunto_oficio = numeracaoGabCoordenadorCrhAssuntos.id_assunto
            INNER JOIN numeracaoDepartamento on numeracaoGabCoordenadorCrhOficios.setorElaboracao_oficio = numeracaoDepartamento.id_deparatamento
            WHERE exclusao_oficio = 0  and numero_oficio like :numero_oficio OR
            exclusao_oficio = 0  and executor_oficio like :executor_oficio OR
            exclusao_oficio = 0  and assunto_oficio like :assunto_oficio OR  
            exclusao_oficio = 0  and  numeracaoGabCoordenadorCrhAssuntos.assunto LIKE :assuntooficio OR
            exclusao_oficio = 0  and numeracaoDepartamento.nome_departamento LIKE :setoroficio
            ORDER BY id_oficio DESC";

            $listar_oficio = $this->connect->prepare($newListar);
            $listar_oficio->bindParam(':numero_oficio', $ParLike, PDO::PARAM_INT);
            $listar_oficio->bindParam(':executor_oficio', $ParLike, PDO::PARAM_STR);
            $listar_oficio->bindParam(':assunto_oficio', $ParLike, PDO::PARAM_INT);
            $listar_oficio->bindParam(':assuntooficio', $ParLike, PDO::PARAM_STR);
            $listar_oficio->bindParam(':setoroficio', $ParLike, PDO::PARAM_STR);
            $listar_oficio->execute();

            if (($listar_oficio) and ($listar_oficio->rowCount() != 0)) {
                while ($result_oficio = $listar_oficio->fetch(PDO::FETCH_ASSOC)) {
                    extract($result_oficio);
                    $lista_oficio[$id_oficio] = [
                        'id_oficio' => $id_oficio,
                        'numero_oficio' => $numero_oficio,
                        'assunto_oficio' => $assunto_oficio,
                        'assuntooficio' => $assuntooficio,
                        'datElaboracao_oficio' => $datElaboracao_oficio,
                        'executor_oficio' => $executor_oficio,
                        'setorElaboracao_oficio' => $setorElaboracao_oficio,
                        'setoroficio' => $setoroficio,
                        'observacao_oficio' => $observacao_oficio
                    ];
                }
                http_response_code(200);
                echo json_encode($lista_oficio);
            }
        }
    }

    public function excluirOficio($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_oficio_list = "UPDATE numeracaoGabCoordenadorCrhOficios
        SET automaticoExclusao_oficio = GETDATE(), exclusao_oficio = 1 
        WHERE id_oficio= :id";

        $exclusaooficio = $this->connect->prepare($query_oficio_list);
        $exclusaooficio->bindParam(':id', $dados['id_oficio']);
        $exclusaooficio->execute();
    }
}