<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\Usuario;
use App\Configuracion\ResponseHTTP;
use Exception;

class UsuarioController {
    
    public function registrar() {
        try {
            // Obtener datos del POST
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar tipo de usuario
            if (!isset($data['tipo_usuario']) || !in_array($data['tipo_usuario'], ['persona', 'empresa'])) {
                return ResponseHTTP::status400('Tipo de usuario inválido');
            }

            // Validar campos según tipo de usuario
            if ($data['tipo_usuario'] === 'persona') {
                // Validar campos requeridos para persona
                $camposRequeridos = ['nombre', 'apellido', 'dni', 'edad', 'telefono', 'email', 'contraseña', 'rol'];
            } else {
                // Validar campos requeridos para empresa
                $camposRequeridos = ['nombreEmpresa', 'email', 'contraseña', 'rol', 'telefonoEmpresa', 'registroFiscal', 'direccion'];
            }

            // Verificar campos requeridos
            foreach ($camposRequeridos as $campo) {
                if (!isset($data[$campo]) || empty($data[$campo])) {
                    return ResponseHTTP::status400("El campo $campo es requerido");
                }
            }

            // Validar rol
            $rolesPermitidos = ['Donante', 'Voluntario', 'Organizador'];
            $rol = $data['tipo_usuario'] === 'persona' ? $data['rol'] : $data['rol'];
            if (!in_array(ucfirst($rol), $rolesPermitidos)) {
                return ResponseHTTP::status400('Rol inválido');
            }

            // Validar formato de email
            $email = $data['tipo_usuario'] === 'persona' ? $data['email'] : $data['email'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ResponseHTTP::status400('Formato de email inválido');
            }

            // Verificar si el email ya existe
            if (Usuario::emailExists($email)) {
                return ResponseHTTP::status400('El email ya está registrado');
            }

            // Registrar usuario
            $resultado = Usuario::registrarUsuario($data);

            return ResponseHTTP::status200('Usuario registrado exitosamente');

        } catch (Exception $e) {
            error_log("Error en UsuarioController::registrar - " . $e->getMessage());
            
            // Manejar errores específicos
            if (strpos($e->getMessage(), 'email ya está registrado') !== false) {
                return ResponseHTTP::status400('El email ya está registrado');
            }
            
            return ResponseHTTP::status500('Error al registrar usuario: ' . $e->getMessage());
        }
    }
}
