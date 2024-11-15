<?php
namespace App\Configuracion; //nombre de espacios

use Dotenv\Dotenv; //variables de entorno https://github.com/vlucas/phpdotenv 
use Firebase\JWT\JWT; //para generar nuestro JWT https://github.com/firebase/php-jwt
use Bulletproof\Image;
use Exception;
class security {

    private static $jwt_data;//Propiedad para guardar los datos decodificados del JWT 

    /*METODO para Acceder a la secret key para crear el JWT*/
    final public static function secretKey()
    {
        //cargamos las variables de entorno en el archivo .env
        $dotenv = Dotenv::createImmutable(dirname(__DIR__,2)); //nuestras variables de entorno estaran en la raiz
                    // del proyecto (el numero dos son los niveles a lo externo, para llegar al directorio raiz)
        $dotenv->load(); //cargando las variables de entorno
        return $_ENV['SECRET_KEY']; //le doy un nombre a nuestra variable de entorno y la retornamos
        //en realidad lo que sucede aqui es por medio de la superglobal $_ENV creamos una variable de entorno
    }

    /*METODO para Encriptar la contraseña del usuario*/
    final public static function createPassword(string $pass)
    {
        $pass = password_hash($pass,PASSWORD_DEFAULT); //metodo para encriptar mediante hash
        //recibe 2 parametros el primero el la cadena (pass) y el segundo es el metodo de encriptación (por defecto BCRIPT)
        return $pass;
    }

    /*Metodo para Validar que las contraseñas coincidan o sean iguales*/
    final public static function validatePassword(string $pw , string $pwh)
    {
        if (password_verify($pw,$pwh)) {
            return true;
        } else {
            error_log('La contraseña es incorrecta');
            return false;
        }       
    }

    /*MEtodo para crear JWT*/
    /*PARAM: 1.	SECRET_KEY
             2.	ARRAY con la data que queremos encriptar*/

    final public static function createTokenJwt(string $key , array $data)
    {
        $payload = array ( //Cuerpo del JWT
            "iat" => time(),  //clave que almacena el tiempo en el que creamos el JWT
            "exp" => time() + (60*60*6), //clave que almacena el tiempo actual en segundos que expira el JWT
            //si solo colocamos 10 entonces expirara en 10 segundos
            "data" => $data //clave que almacena la data encriptada
        );
        
        //creamos el JWT recibe varios parametros pero nos interesa el payload y la key en el metodo encode de JWT
        $jwt = JWT::encode($payload,$key);
        print_r($jwt);
        return $jwt;
    }

    /*Validamos que el JWT sea correcto*/
    //recibimos dos parametros uno es un array y otro es la KEY para decifrar nuestro JWT
    final public static function validateTokenJwt(string $key)
    {
        //usaremos el metodo getallheader() el que Recupera todas las cabeceras de petición HTTP
        //buscaremos la cabecera Autorization, sino existe la detiene y manda un mensaje de error
        if (!isset(getallheaders()['Authorization'])) {
            //echo "El token de acceso en requerido";
            die(json_encode(ResponseHttp::status400()));            
            exit;
        }
        try {
            //recibimos el token de acceso y creamos el array 
            //se veria mas o menos asi 
            // $token = "Bearer token"; posicion 0 y posicion 1
            $jwt = explode(" " ,getallheaders()['Authorization']);
            $data = JWT::decode($jwt[1],$key,array('HS256')); //param1: token, param2: clave, param3: metodo por defecto de encriptacion 
            //necesitamos crear un array asociativo para poder retornarlo y que sea mas facil recorrerlo
            //1. definimos el atributo 
            //private static $jwt_data;//Propiedad para guardar los datos decodificados del JWT 

            self::$jwt_data = $data; //le pasamos el jwt decodificado y lo retornamos
            return $data;
            exit;
        } catch (Exception $e) {
            error_log('Token invalido o expiro'. $e);
            die(json_encode(ResponseHttp::status401('Token invalido o ha expirado'))); //funcion que manda un mj y termina ejecucion 
        }
    }

    /*Devolver los datos del JWT decodificados en un array asociativo*/
    final public static function getDataJwt()
    {
        $jwt_decoded_array = json_decode(json_encode(self::$jwt_data),true);
        return $jwt_decoded_array['data'];
        exit;
    }


    /* TERMINA LA CLASE SECURITY */






    /*Subir Imagen al servidor*/
    final public static function uploadImage($file,$name)
    {
        $file = new Image($file);
 
        $file->setMime(array('png','jpg','jpeg'));//formatos admitidos
        $file->setSize(10000,500000);//Tamaño admitidos es Bytes
        $file->setDimension(200,200);//Dimensiones admitidas en Pixeles
        $file->setLocation('public/Images');//Ubicación de la carpeta

        if ($file[$name]) {
            $upload = $file->upload();            
            if ($upload) {
                $imgUrl = UrlBase::urlBase .'/public/Images/'. $upload->getName().'.'.$upload->getMime();
                $data = [
                    'path' => $imgUrl,
                    'name' => $upload->getName() .'.'. $upload->getMime()
                ];
                return $data;               
            } else {
                die(json_encode(ResponseHttp::status400($file->getError())));               
            }
        }
    }

    /*Subir fotos en base64*/
    final public static function uploadImageBase64(array $data, string $name) 
    {        
        $token = bin2hex(random_bytes(32).time()); 
        $name_img = $token . '.png';
        $route = dirname(__DIR__, 2) . "/public/Images/{$name_img}";        
    
        //Decodificamos la imagen
        $img_decoded = base64_decode(
            preg_replace('/^[^,]*,/', '', $data[$name])
        );
    
        $v = file_put_contents($route,$img_decoded);
    
        //Validamos si se subio la imagen
        if ($v) {
            return UrlBase::urlBase . "/public/Images/{$name_img}";
        } else {
            unlink($route);
            die(json_encode(ResponseHttp::status500('No se puede subir la imagen')));
        }   
        
    }
}