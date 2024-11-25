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
        // Llamar a la función getAllUsuarios para obtener las personas y empresas
        $result = usuario::getAllUsuarios();

        // Verificar si los datos fueron obtenidos correctamente
        if ($result) {
            // Filtrar las personas que tienen datos válidos
            $persona = array_filter($result['persona'], function($persona) {
                return !is_null($persona['Nombre']) && !is_null($persona['Apellido']);
            });

            // Filtrar las empresas que tienen datos válidos
            $empresa = array_filter($result['empresa'], function($empresa) {
                return !is_null($empresa['nombreEmpresa']) && !empty($empresa['nombreEmpresa']);
            });

            // Re-indexar los arrays después de filtrarlos
            $persona = array_values($persona);
            $empresa = array_values($empresa);

            // Retornar la respuesta en formato JSON
            return json_encode([
                'status' => 'success',
                'persona' => $persona,  // Datos de personas
                'empresa' => $empresa   // Datos de empresas
            ]);
        } else {
            // Si no se pudieron obtener los usuarios, retornar error
            return json_encode([
                'status' => 'error',
                'message' => 'No se pudieron obtener los usuarios.'
            ]);
        }
    }
}
