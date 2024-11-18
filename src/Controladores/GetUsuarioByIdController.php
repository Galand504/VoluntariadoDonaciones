<?php

namespace App\Controladores;

use App\clases\usuario;

class GetUsuarioByIdController {
    /**
     * Obtener un usuario por su ID
     *
     * @param int $idUsuario ID del usuario
     * @return string Respuesta en formato JSON
     */
    public function getUsuarioById($idUsuario) {
        $result = usuario::getUsuarioById($idUsuario);

        if ($result) {
            return json_encode([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'message' => 'No se encontró el usuario o ocurrió un error.'
            ]);
        }
    }
}
