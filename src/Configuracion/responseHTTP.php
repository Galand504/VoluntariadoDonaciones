<?php
namespace App\Configuracion;

class responseHTTP {
    public static $mensaje = array(
        'status' => '',
        'message' => '',
        'data' => null
    );

    final public static function status200(string|array $res){
        http_response_code(200);
        self::$mensaje['status'] = 'OK';
        self::$mensaje['message'] = is_string($res) ? $res : 'OK';
        self::$mensaje['data'] = is_array($res) ? $res : null;
        return self::$mensaje;
    }

    final public static function status201(string|array $res = 'Recurso creado exitosamente!'){
        http_response_code(201);
        self::$mensaje['status'] = 'OK';
        self::$mensaje['message'] = is_string($res) ? $res : 'Recurso creado exitosamente!';
        self::$mensaje['data'] = is_array($res) ? $res : null;
        return self::$mensaje;
    }

    final public static function status400(string|array $res = 'Formato de solicitud incorrecto!'){
        http_response_code(400);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = is_string($res) ? $res : 'Formato de solicitud incorrecto!';
        self::$mensaje['data'] = is_array($res) ? $res : null;
        return self::$mensaje;
    }

    final public static function status401(string|array $res = 'No tiene privilegios para acceder al recurso!'){
        http_response_code(401);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = is_string($res) ? $res : 'No tiene privilegios para acceder al recurso!';
        self::$mensaje['data'] = is_array($res) ? $res : null;
        return self::$mensaje;
    }

    final public static function status404(string|array $res = 'No existe el recurso solicitado!'){
        http_response_code(404);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = is_string($res) ? $res : 'No existe el recurso solicitado!';
        self::$mensaje['data'] = is_array($res) ? $res : null;
        return self::$mensaje;
    }

    final public static function status500(string|array $res = 'Se ha producido un error en el servidor!'){
        http_response_code(500);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = is_string($res) ? $res : 'Se ha producido un error en el servidor!';
        self::$mensaje['data'] = is_array($res) ? $res : null;
        return self::$mensaje;
    }

    /**
     * Respuesta para acceso prohibido (403)
     * @param string|array $res Mensaje de error o datos
     * @return array
     */
    public static function status403(string|array $res = 'Acceso prohibido'): array {
        http_response_code(403);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = is_string($res) ? $res : 'Acceso prohibido';
        self::$mensaje['data'] = is_array($res) ? $res : null;
        return self::$mensaje;
    }

    public static function status405($message = "Método no permitido"): array
    {
        return [
            'status' => 'ERROR',
            'message' => $message,
            'code' => 405
        ];
    }
}
