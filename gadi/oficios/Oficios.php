<?php

include_once '../../conexao/Conexao.php';

class Oficios extends Conexao
{

    protected object $connect;
    protected $dados;

    public function cadastrarOficio($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_CadOficio = "INSERT INTO numeracaoGadiOficios
        (
            interessado_oficio, assunto_oficio, datEmissao_oficio, automaticoCriacao_oficio, anoCriacao_oficio, executor_oficio, setorElaboracao_oficio, observacao_oficio,
            excluido_oficio, referencia_banquinho
        )
        VALUES (
            :interessado_oficio, :assunto_oficio, :datEmissao_oficio, GETDATE(), YEAR(GETDATE()), :executor_oficio, :setorElaboracao_oficio, :observacao_oficio,
             0, :referencia_banquinho
        )";

        $cadOficio = $this->connect->prepare($query_CadOficio);
        $cadOficio->bindParam(':interessado_oficio', $dados['oficio']['interessado_oficio'], PDO::PARAM_STR);
        $cadOficio->bindParam(':assunto_oficio', $dados['oficio']['assunto_oficio'], PDO::PARAM_STR);
        $cadOficio->bindParam(':datEmissao_oficio', $dados['oficio']['datEmissao_oficio']);
        $cadOficio->bindParam(':executor_oficio', $dados['oficio']['executor_oficio'], PDO::PARAM_STR);
        $cadOficio->bindParam(':setorElaboracao_oficio', $dados['oficio']['setorElaboracao_oficio'], PDO::PARAM_INT);
        $cadOficio->bindParam(':observacao_oficio', $dados['oficio']['observacao_oficio'], PDO::PARAM_STR);
        $cadOficio->bindParam(':referencia_banquinho', $dados['oficio']['referencia_banquinho'], PDO::PARAM_INT);

        $cadOficio->execute();
    }

    public function listarOficios()
    {

        $conn = new Conexao();

        $this->connect = $conn->conectar();

        $query_listOficios = "SELECT id_oficio, numero_oficio, interessado_oficio, assunto_oficio, datEmissao_oficio, automaticoCriacao_oficio, anoCriacao_oficio, executor_oficio, 
        numeracaoSetor.nome_setor as area_oficio, observacao_oficio, referencia_banquinho 
        FROM numeracaoGadiOficios
        INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiOficios.setorElaboracao_oficio 
        WHERE excluido_oficio = 0 
        ORDER BY id_oficio DESC ";

        $result_listOficios = $this->connect->prepare($query_listOficios);
        $result_listOficios->execute();

        if (($result_listOficios) and ($result_listOficios->rowCount() != 0)) {
            while ($resultOficio = $result_listOficios->fetch(PDO::FETCH_ASSOC)) {
                extract($resultOficio);
                $listaOficio[$id_oficio] = [
                    'id_oficio' => $id_oficio,
                    'numero_oficio' => $numero_oficio,
                    'interessado_oficio' => $interessado_oficio,
                    'assunto_oficio' => $assunto_oficio,
                    'datEmissao_oficio' => $datEmissao_oficio,
                    'executor_oficio' => $executor_oficio,
                    'area_oficio' => $area_oficio,
                    'observacao_oficio' => $observacao_oficio,
                    'referencia_banquinho' => $referencia_banquinho
                ];
            }
            http_response_code(200);
            echo json_encode(($listaOficio));
        }
    }

    public function visualizarOficios($id)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_oficio_list = "SELECT
            id_oficio, numero_oficio, interessado_oficio, assunto_oficio, datEmissao_oficio, automaticoCriacao_oficio, anoCriacao_oficio, executor_oficio, 
        numeracaoSetor.nome_setor as area_oficio, numeracaoSetor.id_setor AS codSetor_oficio, observacao_oficio, referencia_banquinho 
        FROM numeracaoGadiOficios
        INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiOficios.setorElaboracao_oficio 
        WHERE excluido_oficio = 0 AND id_oficio = :id";

        $result_oficios = $this->connect->prepare($query_oficio_list);
        $result_oficios->bindParam(':id', $id, PDO::PARAM_INT);
        $result_oficios->execute();

        if (($result_oficios) and ($result_oficios->rowCount() != 0)) {
            $row_oficio = $result_oficios->fetch(PDO::FETCH_ASSOC);
            extract($row_oficio);

            $oficio = [
                    'id_oficio' => $id_oficio,
                    'numero_oficio' => $numero_oficio,
                    'interessado_oficio' => $interessado_oficio,
                    'assunto_oficio' => $assunto_oficio,
                    'datEmissao_oficio' => $datEmissao_oficio,
                    'executor_oficio' => $executor_oficio,
                    'codSetor_oficio' => $codSetor_oficio,
                    'area_oficio' => $area_oficio,
                    'observacao_oficio' => $observacao_oficio,
                    'referencia_banquinho' => $referencia_banquinho
            ];
            $response = [
                "erro" => false,
                "mensagem" => $oficio
            ];
        } else {
            $response = [
                "erro" => true,
                "mensagem" => "OFÍCIO NÃO ENCONTRADO, FAVOR VALIDAR AS INFORMAÇÕES (ERRO 1-B)"
            ];
        }
        http_response_code(200);
        echo json_encode($response);
    }

    public function editarOficio($dados)
    {
        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_oficio_list = "UPDATE numeracaoGadiOficios
        SET interessado_oficio = :interessado_oficio, assunto_oficio = :assunto_oficio, executor_oficio = :executor_oficio ,
        setorElaboracao_oficio = :setorElaboracao_oficio , observacao_oficio = :observacao_oficio , referencia_banquinho = :referencia_banquinho 
        WHERE id_oficio = :id AND excluido_oficio = 0 ";    

        $editOficio = $this->connect->prepare($query_oficio_list);
        $editOficio->bindParam(':interessado_oficio', $dados['interessado_oficio'], PDO::PARAM_STR);
        $editOficio->bindParam(':assunto_oficio', $dados['assunto_oficio'], PDO::PARAM_STR);
        $editOficio->bindParam(':executor_oficio', $dados['executor_oficio'], PDO::PARAM_STR);
        $editOficio->bindParam(':setorElaboracao_oficio', $dados['setorElaboracao_oficio'], PDO::PARAM_INT);
        $editOficio->bindParam(':observacao_oficio', $dados['observacao_oficio'], PDO::PARAM_STR);
        $editOficio->bindParam(':referencia_banquinho', $dados['referencia_banquinho'], PDO::PARAM_INT);
        $editOficio->bindParam(':id', $dados['id_oficio'], PDO::PARAM_STR);
        $editOficio->execute();

        if ($editOficio->rowCount()) {
            return "Ofício Alterado";
        } else {
            return "Ofício não alterado, Favor Validar (Error -> 01B)";
        }
    }

    public function excluirOficio($dados)
    {

        $conn = new Conexao();
        $this->connect = $conn->conectar();

        $query_oficio_list = "UPDATE numeracaoGadiOficios
        SET excluido_oficio = 1,  automatico_exclusao = GETDATE() WHERE id_oficio = :id";

        $exclusaoOficio = $this->connect->prepare($query_oficio_list);
        $exclusaoOficio->bindParam(':id', $dados['id_oficio'], PDO::PARAM_INT);

        $exclusaoOficio->execute();

        if ($exclusaoOficio->rowCount()) {
            return "Oficio Excluído com Sucesso";
        } else {
            return "Oficio não excluído, Favor Validar (Error -> 01B)";
        }
    }

    public function newListarOficio($BuscaFinal)
    {
        if ($BuscaFinal == null) {
            $BFetchFull = $this->listarOficios();
        } else {

            $conn = new Conexao();
            $this->connect = $conn->conectar();

            $ParLike = '%' . $BuscaFinal . '%';

            $BFetch = "SELECT id_oficio, numero_oficio, interessado_oficio, assunto_oficio, datEmissao_oficio, executor_oficio, 
            numeracaoSetor.nome_setor as area_oficio,numeracaoSetor.id_setor AS codSetor_oficio,  observacao_oficio, referencia_banquinho 
            FROM numeracaoGadiOficios
            INNER JOIN  numeracaoSetor on numeracaoSetor.id_setor = numeracaoGadiOficios.setorElaboracao_oficio 
            WHERE excluido_oficio = 0 AND numero_oficio LIKE :numero_oficio OR
            excluido_oficio = 0 AND interessado_oficio LIKE :interessado_oficio OR
            excluido_oficio = 0 AND assunto_oficio LIKE :assunto_oficio OR
            excluido_oficio = 0 AND executor_oficio LIKE :executor_oficio OR
            excluido_oficio = 0 AND numeracaoSetor.nome_setor LIKE :nome_setor
            ORDER BY id_oficio DESC ";
            
            $BFetchFull = $this->connect->prepare($BFetch);

            $BFetchFull->bindParam(':numero_oficio', $ParLike, PDO::PARAM_INT);
            $BFetchFull->bindParam(':interessado_oficio', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':assunto_oficio', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':executor_oficio', $ParLike, PDO::PARAM_STR);
            $BFetchFull->bindParam(':nome_setor', $ParLike, PDO::PARAM_STR);

            $BFetchFull->execute();

            $I = 0;

            if (($BFetchFull) and ($BFetchFull->rowCount() != 0)) {
                while ($Fetch = $BFetchFull->fetch(PDO::FETCH_ASSOC)) {
                    extract($Fetch);

                    $listaOficio[$I] = [

                        'id_oficio' => $Fetch['id_oficio'],
                        'numero_oficio' => $Fetch['numero_oficio'],
                        'interessado_oficio' => $Fetch['interessado_oficio'],
                        'assunto_oficio' => $Fetch['assunto_oficio'],
                        'datEmissao_oficio' => $Fetch['datEmissao_oficio'],
                        'executor_oficio' => $Fetch['executor_oficio'],
                        'area_oficio' => $Fetch['area_oficio'],
                        'observacao_oficio' => $Fetch['observacao_oficio'],
                        'referencia_banquinho' => $Fetch['referencia_banquinho']
                    ];
                    $I++;
                }
                http_response_code(200);
                echo json_encode($listaOficio);
            }
        }
    }
}