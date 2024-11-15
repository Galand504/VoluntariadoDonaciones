<?php

namespace App\Base; //nombre de espacios con la carpeta donde esta ubicado este archivo
use App\Configuracion\responseHTTP;
use PDO;
require __DIR__.'/dataDB.php';

class Database{
    private  $host = "localhost";
    private static $user = "root";
    private static $pass = "";

    final public static function inicializar($host, $user, $pass){
        //this or self?
        self::$host = $host;
        self::$user = $user;
        self::$pass = $pass;
    }

    final public static function getConnection(){
        try{
            //opciones de conexion
            $opt = [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC];
            $pdo = new PDO(self::$host,self::$user,self::$pass, $opt);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            error_log("COnexión exitosa");
            return $pdo;
        }catch(\PDOException $e){
            error_log("Error en la conexión a la BD! ERROR: ".$e);
            die(json_encode(responseHTTP::status500()));

        }
    }
   
}