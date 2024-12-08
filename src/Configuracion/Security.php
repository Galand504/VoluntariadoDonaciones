<?php
namespace App\Configuracion;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class Security {
    /**
     * Crea un hash seguro de la contraseña
     * @param string $password Contraseña en texto plano
     * @return string Hash de la contraseña
     */
    public static function createPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Obtiene la clave secreta para JWT
     * @return string
     */
    public static function secretKey(): string {
        return $_ENV['JWT_SECRET_KEY'] ?? 'tu_clave_secreta_por_defecto';
    }

    /**
     * Genera un nuevo token JWT
     * @param string $secretKey Clave secreta
     * @param array $userData Datos del usuario
     * @return string Token JWT
     */
    public static function createTokenJwt(string $secretKey, array $userData): string {
        $time = time();
        
        $payload = [
            'iat' => $time,
            'exp' => $time + 3600, // 1 hora
            'data' => $userData
        ];

        return JWT::encode($payload, $secretKey, 'HS256');
    }

    /**
     * Valida y decodifica un token JWT
     * @param string $secretKey Clave secreta
     * @return bool
     */
    public static function validateTokenJwt(string $secretKey): bool {
        try {
            $token = self::getTokenFromHeader();
            if (!$token) {
                return false;
            }

            JWT::decode($token, new Key($secretKey, 'HS256'));
            return true;

        } catch (Exception $e) {
            error_log("Error validando token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el token del header Authorization
     */
    private static function getTokenFromHeader(): ?string {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (empty($authHeader)) {
            return null;
        }

        return str_replace('Bearer ', '', $authHeader);
    }

    /**
     * Verifica si un usuario tiene permiso para acceder a un recurso
     * @param array $tokenData Datos del token
     * @param string $requiredRole Rol requerido
     * @return bool
     */
    public static function hasPermission(array $tokenData, string $requiredRole): bool {
        return isset($tokenData['user_data']['role']) && 
               $tokenData['user_data']['role'] === $requiredRole;
    }

    public static function getTokenData($token) {
        return JWT::decode($token, new Key(self::secretKey(), 'HS256'));
    }
}



