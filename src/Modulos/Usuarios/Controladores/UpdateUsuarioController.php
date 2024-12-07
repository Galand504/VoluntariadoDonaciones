<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use App\Configuracion\responseHTTP;
use App\Configuracion\Security;
use Exception;

class UpdateUsuarioController {
    /**
     * Método para actualizar un usuario
     *
     * @param array $data Datos enviados para la actualización
     * @return string Respuesta en formato JSON
     */
    public function updateUsuario($data) {
        try {
            // Obtener headers y extraer el token
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? '';
            
            // Extraer el token Bearer
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
                Security::validateTokenJwt($token);
            } else {
                return json_encode(responseHTTP::status401('Token no proporcionado o formato inválido'));
            }

            // Validar datos mínimos
            if (!isset($data['id_usuario'], $data['email'], $data['tipo'])) {
                error_log("Faltan datos obligatorios");
                return json_encode(responseHTTP::status400('Faltan datos obligatorios: id_usuario, email y tipo.'));
            }

            // Normalizar el tipo a mayúsculas
            $tipo = ucfirst(strtolower($data['tipo']));

            // Validar que el tipo sea válido
            if (!in_array($tipo, ['Persona', 'Empresa'])) {
                error_log("Tipo de usuario no válido: " . $tipo);
                return json_encode(responseHTTP::status400('Tipo de usuario no válido. Debe ser "Persona" o "Empresa".'));
            }

            // Variable para el parámetro de referencia
            $p_id_usuario = 0; // Inicializar con un valor

            // Log para depuración
            error_log("Datos antes de la actualización: " . print_r([
                'id_usuario' => $data['id_usuario'],
                'tipo' => $tipo,
                'rol' => $data['rol'] ?? null
            ], true));

            // Llamar al método de actualización con el orden correcto de parámetros
            $result = usuario::updateUsuario(
                intval($data['id_usuario']),    // id_usuario
                $data['nombre'] ?? null,        // nombre
                $data['apellido'] ?? null,      // apellido
                $data['email'],                 // email
                isset($data['contraseña']) ? $data['contraseña'] : null, // contraseña
                $data['telefono'] ?? null,      // telefono
                $data['dni'] ?? null,           // dni
                $data['edad'] ?? null,          // edad
                $p_id_usuario,                  // p_id_usuario (parámetro por referencia)
                $data['rol'] ?? null,           // rol
                $tipo,                          // tipo
                $data['nombreEmpresa'] ?? null, // nombreEmpresa
                $data['razonSocial'] ?? null,   // razonSocial
                $data['telefonoEmpresa'] ?? null, // telefonoEmpresa
                $data['direccion'] ?? null,     // direccion
                $data['registroFiscal'] ?? null // registroFiscal
            );

            if ($result) {
                return json_encode(responseHTTP::status200('Usuario actualizado correctamente'));
            } else {
                error_log("La actualización falló sin lanzar excepción");
                return json_encode(responseHTTP::status500('No se pudo actualizar el usuario.'));
            }

        } catch (Exception $e) {
            error_log("Error en UpdateUsuarioController: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return json_encode(responseHTTP::status500('Error al actualizar el usuario: ' . $e->getMessage()));
        }
    }
}

