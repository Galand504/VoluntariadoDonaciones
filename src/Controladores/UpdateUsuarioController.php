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
    public function updateusuario($data) {
        // Validar que los datos mínimos están presentes
        if (!isset($data['id_usuario'], $data['email'], $data['contraseña'], $data['Rol'], $data['Tipo'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Faltan datos obligatorios: id_usuario, email, contraseña y tipo.'
            ]);
        }

        // Asignar valores con manejo de valores opcionales
        $idUsuario = $data['id_usuario'];
        $nombre = $data['nombre'] ?? null;
        $nombreEmpresa = $data['nombreEmpresa'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $email = $data['email'];
        $contraseña = $data['contraseña'];
        $telefono = $data['telefono'] ?? null;
        $dni = $data['dni'] ?? null;
        $edad = $data['edad'] ?? null;
        $Rol = $data['Rol'] ?? null;
        $Tipo = $data['Tipo'];
        $razonSocial = $data['razonSocial'] ?? null;
        $telefonoEmpresa = $data['telefonoEmpresa'] ?? null;
        $direccion = $data['direccion'] ?? null;
        $registroFiscal = $data['registroFiscal'] ?? null;

        // Llamar al método `updateUsuario` de la clase usuario
        $result = usuario::updateUsuario(
            $idUsuario,
            $nombre,
            $nombreEmpresa,
            $apellido,
            $email,
            $contraseña,
            $telefono,
            $dni,
            $edad,
            $Rol,
            $Tipo,
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
