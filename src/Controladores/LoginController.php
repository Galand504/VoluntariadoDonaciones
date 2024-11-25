<?php

namespace App\Controladores;

use App\clases\usuario;
use App\Configuracion\Security;

class loginController {

    public function login($email, $password) {
        // Obtener el usuario de la base de datos usando su correo
        $usuario = usuario::getUsuarioByEmail($email);
    
        // Verificar si el usuario existe
        if (!$usuario) {
            return json_encode([
                'status' => 'error',
                'message' => 'El correo electrónico no está registrado.'
            ]);
        }
    
        // Verificar la contraseña usando el método de la clase Security
        if (!Security::validatePassword($password, $usuario['contraseña'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Contraseña incorrecta.'
            ]);
        }
    
        // Si el usuario es válido, agregar el tipo (persona o empresa)
        $data = [
            'id_usuario' => $usuario['id_usuario'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
            'tipo' => $usuario['tipo']  // Agregar el tipo
        ];
    
        // Crear el token JWT
        $jwt = Security::createTokenJwt(Security::secretKey(), $data);
    
        // Retornar el JWT y la redirección según el rol y tipo
        return json_encode([
            'status' => 'success',
            'message' => 'Login exitoso.',
            'jwt' => $jwt,
            'rol' => $usuario['rol'],
            'tipo' => $usuario['tipo']  // Devolver el tipo junto con el rol
        ]);
    }
}     

