<?php

namespace App\Controladores;

use App\clases\usuario;

class UpdateUsuarioController {
    /**
     * Método para actualizar un usuario
     *
     * @param array $data Datos enviados para la actualización
     * @return string Respuesta en formato JSON
     */
    public function updateUsuario($data) {
        // Validar que los datos mínimos están presentes
        if (!isset($data['id_usuario'], $data['email'], $data['contraseña'], $data['tipo'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Faltan datos obligatorios: id_usuario, email, contraseña y tipo.'
            ]);
        }

        // Asignar valores con manejo de valores opcionales
        $id_usuario = $data['id_usuario'];
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $email = $data['email'];
        $contraseña = $data['contraseña'];
        $telefono = $data['telefono'] ?? null;
        $dni = $data['dni'] ?? null;
        $edad = $data['edad'] ?? null;
        $rol = $data['rol'] ?? null;
        $tipo = $data['tipo'] ?? null;
        $nombreEmpresa = $data['nombreEmpresa'] ?? null;
        $razonSocial = $data['razonSocial'] ?? null;
        $telefonoEmpresa = $data['telefonoEmpresa'] ?? null;
        $direccion = $data['direccion'] ?? null;
        $registroFiscal = $data['registroFiscal'] ?? null;

        // Llamar al método `updateUsuario` de la clase usuario
        $result = usuario::updateUsuario(
            $id_usuario,
            $nombre,
            $apellido,
            $email,
            $contraseña,
            $telefono,
            $dni,
            $edad,
            $rol,
            $tipo,
            $nombreEmpresa,
            $razonSocial,
            $telefonoEmpresa,
            $direccion,
            $registroFiscal
        );

        // Verificar el resultado y devolver respuesta
        if ($result) {
            return json_encode([
                'status' => 'success',
                'message' => 'Usuario actualizado correctamente.'
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'message' => 'No se pudo actualizar el usuario.'
            ]);
        }
    }
}
