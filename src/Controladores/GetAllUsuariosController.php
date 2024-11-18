<?php

namespace App\Controladores;

use App\clases\usuario;

class GetAllUsuariosController {
    /**
     * Obtener todos los usuarios
     *
     * @return string Respuesta en formato JSON
     */
    public function getAllUsuarios() {
        $result = usuario::getAllUsuarios();

        if ($result) {
            return json_encode([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'message' => 'No se pudieron obtener los usuarios.'
            ]);
        }
    }
}
