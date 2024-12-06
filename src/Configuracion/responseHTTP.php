<?php
namespace App\Configuracion;

class ResponseHTTP {
    public static $mensaje = array(
        'status' => '',
        'message' => '',
        'data' => ''
    );

    final public static function status200(string $res){
        http_response_code(200);
        self::$mensaje['status'] = 'OK';
        self::$mensaje['message'] = $res; //la variable res es el mensaje/respuesta que proviene del usuario
        return self::$mensaje;
    }

    final public static function status201(string $res = 'Recurso creado exitosamente!'){
        http_response_code(201);
        self::$mensaje['status'] = 'OK';
        self::$mensaje['message'] = $res; //la variable res es el mensaje
        return self::$mensaje;
    }

    final public static function status400(string $res = 'Formato de solicitud incorrecto!'){
        http_response_code(400);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = $res; //la variable res es el mensaje
        return self::$mensaje;
    }

    final public static function status401(string $res = 'No tiene privilegios para acceder al recurso!'){
        http_response_code(401);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = $res; //la variable res es el mensaje
        return self::$mensaje;
    }

    final public static function status404(string $res = 'No existe el recurso solicitado!'){
        http_response_code(404);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = $res; //la variable res es el mensaje
        return self::$mensaje;
    }

    final public static function status500(string $res = 'Se ha producido un error en el servidor!'){
        http_response_code(500);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = $res; //la variable res es el mensaje
        return self::$mensaje;
    }

    /**
     * Respuesta para acceso prohibido (403)
     * @param string $mensaje Mensaje de error
     * @return array
     */
    public static function status403(string $mensaje): array {
        return [
            'status' => 403,
            'message' => $mensaje
        ];
    }
}
