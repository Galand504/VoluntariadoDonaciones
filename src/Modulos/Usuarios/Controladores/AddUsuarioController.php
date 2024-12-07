<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\usuario;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;
class AddUsuarioController
{
    public function addUsuario($data){
        try {


            // Validaciones iniciales de campos requeridos
            $Tipo = $data['Tipo'] ?? null;
            if (!$Tipo || !in_array($Tipo, ['Empresa', 'Persona'])) {
                return $this->jsonResponse(ResponseHTTP::status400('Tipo de usuario no válido.'));
            }

            $email = $data['email'] ?? null;
            $contraseña = $data['contraseña'] ?? null;
            $Rol = $data['Rol'] ?? null;

            // Validaciones básicas comunes
            if (empty($email) || empty($contraseña) || empty($Rol)) {
                return $this->jsonResponse(ResponseHTTP::status400('Email, contraseña y rol son obligatorios.'));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->jsonResponse(ResponseHTTP::status400('El correo debe tener un formato válido.'));
            }

            // Validaciones adicionales para el correo y contraseña
            if (strlen($contraseña) < 8) {
                return $this->jsonResponse(ResponseHTTP::status400('La contraseña debe tener al menos 8 caracteres.'));
            }

            // Verificar si el correo ya existe en la base de datos
            if (Usuario::emailExists($email)) {
                return $this->jsonResponse(ResponseHTTP::status400('El correo electrónico ya está registrado.'));
            }

            // Validaciones específicas por tipo de usuario
            if ($Tipo === 'Empresa') {
                $nombreEmpresa = $data['nombreEmpresa'] ?? null;
                $razonSocial = $data['razonSocial'] ?? null;
                $registroFiscal = $data['registroFiscal'] ?? null;
                $telefonoEmpresa = $data['telefonoEmpresa'] ?? null;
                $direccion = $data['direccion'] ?? null;

                if (empty($nombreEmpresa) || empty($razonSocial) || empty($registroFiscal) || empty($telefonoEmpresa) || empty($direccion)) {
                    return $this->jsonResponse(ResponseHTTP::status400('Faltan datos obligatorios para la empresa.'));
                }

                // Validaciones adicionales para empresa
                if (strlen($registroFiscal) < 10) {
                    return $this->jsonResponse(ResponseHTTP::status400('El registro fiscal debe tener al menos 10 caracteres.'));
                }

                if (!preg_match('/^[0-9]{10}$/', $telefonoEmpresa)) {
                    return $this->jsonResponse(ResponseHTTP::status400('El teléfono debe contener 10 dígitos numéricos.'));
                }
            } elseif ($Tipo === 'Persona') {
                $nombre = $data['nombre'] ?? null;
                $apellido = $data['apellido'] ?? null;
                $dni = $data['dni'] ?? null;
                $edad = $data['edad'] ?? null;
                $telefono = $data['telefono'] ?? null;

                if (empty($nombre) || empty($apellido) || empty($dni) || empty($edad) || empty($telefono)) {
                    return $this->jsonResponse(ResponseHTTP::status400('Faltan datos obligatorios para la persona.'));
                }

                // Validaciones adicionales para persona
                if (!is_numeric($edad) || $edad < 18 || $edad > 120) {
                    return $this->jsonResponse(ResponseHTTP::status400('La edad debe ser un número entre 18 y 120.'));
                }

                if (!preg_match('/^[0-9]{8}$/', $dni)) {
                    return $this->jsonResponse(ResponseHTTP::status400('El DNI debe contener 8 dígitos numéricos.'));
                }

                if (!preg_match('/^[0-9]{10}$/', $telefono)) {
                    return $this->jsonResponse(ResponseHTTP::status400('El teléfono debe contener 10 dígitos numéricos.'));
                }
            }

            // Llamada al modelo para la creación del usuario
            $p_id_usuario = null; // ID del usuario que se generará

            $resultado = Usuario::addUsuario(
                $email,
                $contraseña,
                $Rol,
                $Tipo,
                $nombre ?? null,
                $apellido ?? null,
                $dni ?? null,
                $edad ?? null,
                $telefono ?? null,
                $p_id_usuario,
                $nombreEmpresa ?? null,
                $direccion ?? null,
                $telefonoEmpresa ?? null,
                $razonSocial ?? null,
                $registroFiscal ?? null
            );

            if ($resultado) {
                // Crear payload para el JWT
                $payload = [
                    'id_usuario' => $p_id_usuario,
                    'email' => $email,
                    'Rol' => $Rol,
                    'Tipo' => $Tipo
                ];

                // Generar el token JWT
                $token = Security::createTokenJwt(Security::secretKey(), $payload);

                // Devolver respuesta exitosa con el token
                return $this->jsonResponse([
                    'status' => 200,
                    'message' => 'Usuario creado correctamente',
                    'token' => $token,
                    'usuario' => [
                        'id_usuario' => $p_id_usuario,
                        'email' => $email,
                        'Rol' => $Rol,
                        'Tipo' => $Tipo
                    ]
                ]);
            }
            
            return $this->jsonResponse(ResponseHTTP::status500('No se pudo crear el usuario.'));
            
        } catch (Exception $e) {
            error_log("Error en AddUsuarioController: " . $e->getMessage());
            return $this->jsonResponse(ResponseHTTP::status500('Error interno del servidor: ' . $e->getMessage()));
        }
    }

    private function jsonResponse($data)
    {
        return json_encode($data);
    }
}
