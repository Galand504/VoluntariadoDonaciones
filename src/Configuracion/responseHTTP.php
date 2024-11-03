<?php
namespace App\Configuracion;

class responseHTTP {
    public static $mensaje = array(
        'status' => '',
        'message' => '',
        'data' => ''
    );

    final public static function status200(string $res) {
        self::$mensaje['status'] = '200';
        self::$mensaje['message'] = $res;
        http_response_code(200);
        return json_encode(self::$mensaje);
    }
}
