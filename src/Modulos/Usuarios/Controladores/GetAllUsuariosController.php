<?php
namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;

class GetAllUsuariosController {
    /**
     * Obtener todos los usuarios
     *
     * @return string Respuesta en formato JSON
     */
    public function getAllUsuarios(): array {
        try {
            // Validar el token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                return ResponseHTTP::status401('Token inválido o expirado.');
            }

            // Obtener todos los usuarios
            $result = usuario::getAllUsuarios();
            if (!$result) {
                return ResponseHTTP::status404('No se encontraron usuarios.');
            }

            return [
                'status' => 200,
                'data' => $result
            ];

        } catch (\Exception $e) {
            return ResponseHTTP::status500('Error interno del servidor: ' . $e->getMessage());
        }
    }
}
