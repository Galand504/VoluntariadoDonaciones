<?php
namespace App\Configuracion;

class errorlogs{
    public static function activa_error_logs(){
        error_reporting(E_ALL);

        ini_set('ignore_repeated_errors',true);
        ini_set('display_errors',false);
        ini_set('log_errors',true);
        ini_set('error_log', dirname(__DIR__). '/Logs/php-error.log');
    }

}
