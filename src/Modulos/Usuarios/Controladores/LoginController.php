<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use Exception;

class LoginController 
{
    public function login($data) {
        try {
            if (!isset($data['email']) || !isset($data['contraseña'])) {
                return [
                    'status' => 400,
                    'message' => 'Email y contraseña son requeridos'
                ];
            }

            $email = $data['email'];
            $contraseña = $data['contraseña'];

            $resultado = Usuario::login($email, $contraseña);

            if ($resultado['status'] === 200) {
                return [
                    'status' => 200,
                    'message' => 'Login exitoso',
                    'token' => $resultado['token'],
                    'user' => $resultado['user']
                ];
            }

            return [
                'status' => 401,
                'message' => $resultado['message'] ?? 'Credenciales inválidas'
            ];

        } catch (Exception $e) {
            error_log("Error en LoginController->login: " . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'Error interno del servidor'
            ];
        }
    }
}     

