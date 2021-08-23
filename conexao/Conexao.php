<?php

class Conexao
{
    public static function conectar() {

        $pdoConfig  = DB_DRIVER . ":". "Server=" . DB_HOST . ";";
        $pdoConfig .= "Database=".DB_NAME.";";

        try {
            if(!isset($connection)){
                $connection =  new PDO($pdoConfig, DB_USER, DB_PASSWORD);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return $connection;
         } catch (PDOException $e) {
            $mensagem = "Drivers disponiveis: " . implode(",", PDO::getAvailableDrivers());
            $mensagem .= "\nErro: " . $e->getMessage();
            throw new Exception($mensagem);
         }
     }
}
define('DB_HOST'        , "172.18.15.14");
define('DB_USER'        , "CRHADM");
define('DB_PASSWORD'    , "adm2006");
define('DB_NAME'        , "CRH_OLD");
define('DB_DRIVER'      , "sqlsrv");
?>