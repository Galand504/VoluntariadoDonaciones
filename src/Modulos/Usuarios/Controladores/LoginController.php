<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;

class LoginController 
{
    public function login($correo, $password): array {
        try {
            // Validar que los campos no estén vacíos
            if (empty($correo) || empty($password)) {
                return ResponseHTTP::status400('El correo y la contraseña son requeridos.');
            }

            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                return ResponseHTTP::status400('Formato de correo electrónico inválido.');
            }

            // Intentar login con las credenciales
            $usuario = new Usuario();
            $resultado = $usuario->login($correo, $password);
            
            if (!$resultado['status']) {
                return ResponseHTTP::status401($resultado['message']);
            }

            // El token ya viene generado desde el método login de usuario
            return [
                'status' => 200,
                'message' => 'Login exitoso',
                'token' => $resultado['token'],
                'user' => $resultado['usuario']
            ];

        } catch (\Exception $e) {
            return ResponseHTTP::status500('Error en el servidor: ' . $e->getMessage());
        }
    }
}     

