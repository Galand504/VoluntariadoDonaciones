<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;

class GetUsuarioByIdController {
    /**
     * Obtener un usuario por su ID
     *
     * @param int $idUsuario ID del usuario
     * @return array Respuesta en formato JSON
     */
    public function getUsuarioById($idUsuario): array {
        try {
            // Validar el token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                return ResponseHTTP::status401('Token inválido o expirado.');
            }

            // Obtener el usuario
            $result = usuario::getUsuarioById($idUsuario);
            if (!$result) {
                return ResponseHTTP::status404('Usuario no encontrado.');
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
