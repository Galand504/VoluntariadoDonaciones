<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use App\Configuracion\responseHTTP;
use App\Configuracion\Security;

class UpdateUsuarioController {
    /**
     * Método para actualizar un usuario
     *
     * @param array $data Datos enviados para la actualización
     * @return string Respuesta en formato JSON
     */
    public function updateUsuario($data) {
        $headers = getallheaders();
        Security::validateTokenJwt($headers);

        // Validar que los datos mínimos están presentes
        if (!isset($data['id_usuario'], $data['email'], $data['contraseña'], $data['tipo'])) {
            return json_encode(responseHTTP::status400('Faltan datos obligatorios: id_usuario, email, contraseña y tipo.'));
        }

        // Asignar valores con manejo de valores opcionales
        $id_usuario = $data['id_usuario'];
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $email = $data['email'];
        $contraseña = password_hash($data['contraseña'], PASSWORD_DEFAULT);
        $telefono = $data['telefono'] ?? null;
        $dni = $data['dni'] ?? null;
        $edad = $data['edad'] ?? null;
        $rol = $data['rol'] ?? null;
        $tipo = $data['tipo'];
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
            return json_encode(responseHTTP::status200('Usuario actualizado correctamente.'));
        } else {
            return json_encode(responseHTTP::status500('No se pudo actualizar el usuario.'));
        }
    }
}

