<?php
use App\Configuracion\errorlogs;
use App\Configuracion\responseHTTP;
use App\Base\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('C:/xampp/htdocs/Crowdfunding/');
$dotenv->load();



$data = array(
    "user" => $_ENV['USER'],
    "password" => $_ENV['PASSWORD'],
    "DB" => $_ENV['DB'],
    "IP" => $_ENV['IP'],
    "port" => $_ENV['PORT']
);

/* conectamos a la base de datos llamando al metodo de la clase que retorna PDO*/
$host = 'mysql:host='.$data['IP'].';'.'port='.$data['port'].';'.'dbname='.$data['DB'];
Database::inicializar($host, $data['user'], $data['password']);