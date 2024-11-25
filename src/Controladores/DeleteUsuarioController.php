<?php

namespace App\Controladores;

use App\clases\usuario;

class DeleteUsuarioController {
    /**
     * Método para eliminar un usuario
     *
     * @param int $idUsuario ID del usuario a eliminar
     * @return string Respuesta en formato JSON
     */
    public function deleteUsuario($id_usuario) {
        // Validar que se envió un ID de usuario
        if (!$id_usuario) {
            return json_encode([
                'status' => 'error',
                'message' => 'Falta el ID del usuario a eliminar.'
            ]);
        }

        // Llamar al método `deleteUsuario` de la clase usuario
        $result = usuario::deleteUsuario($id_usuario);

        // Verificar el resultado y devolver respuesta
        if ($result) {
            return json_encode([
                'status' => 'success',
                'message' => 'Usuario eliminado correctamente.'
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'message' => 'No se pudo eliminar el usuario.'
            ]);
        }
    }
}
