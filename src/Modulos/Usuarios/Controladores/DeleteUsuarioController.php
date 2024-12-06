<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
class DeleteUsuarioController {
    /**
     * Método para eliminar un usuario
     *
     * @param int $idUsuario ID del usuario a eliminar
     * @return array Respuesta en formato JSON
     */
    public function deleteUsuario($idUsuario): array {
        try {
            // Validar el token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                return ResponseHTTP::status401('Token inválido o expirado.');
            }

            // Intentar eliminar el usuario
            $result = usuario::deleteUsuario($idUsuario);
            if (!$result) {
                return ResponseHTTP::status404('Usuario no encontrado.');
            }

            return [
                'status' => 200,
                'message' => 'Usuario eliminado correctamente'
            ];

        } catch (\Exception $e) {
            return ResponseHTTP::status500('Error interno del servidor: ' . $e->getMessage());
        }
    }
}
