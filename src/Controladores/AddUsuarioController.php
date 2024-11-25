<?php
namespace App\Controladores;
use App\clases\usuario;

class AddUsuarioController {

    public function addUsuario($data) {
        // Validar datos básicos dependiendo del tipo
        if ($data['Tipo'] === 'Empresa') {
            // Para tipo 'Empresa', los campos 'dni' y 'edad' no son necesarios
            if (!isset($data['email'], $data['contraseña'], $data['rol'], $data['Tipo'], $data['nombreEmpresa'], $data['razonSocial'], $data['registroFiscal'], $data['telefonoEmpresa'], $data['direccion'])) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Faltan datos obligatorios para la empresa.'
                ]);
            }
        } else if ($data['Tipo'] === 'Persona') {
            // Para tipo 'Persona', se requieren 'dni' y 'edad'
            if (!isset($data['email'], $data['contraseña'], $data['rol'], $data['Tipo'], $data['nombre'], $data['apellido'], $data['telefono'], $data['dni'], $data['edad'])) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Faltan datos obligatorios para la persona.'
                ]);
            }
        } else {
            // Si no se ha especificado un tipo válido
            return json_encode([
                'status' => 'error',
                'message' => 'Tipo de usuario no válido.'
            ]);
        }
    
        // Asignar variables desde el input
        $email = $data['email'];
        $contraseña = $data['contraseña'];
        $rol = $data['rol'];
        $Tipo = $data['Tipo'];
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido' ] ?? null; 
        $dni = $data['dni'] ?? null; 
        $edad = $data['edad'] ?? null; 
        $telefono = $data['telefono'] ?? null;
        $nombreEmpresa = $data['nombreEmpresa'];
        $direccion = $data['direccion'] ?? null;
        $telefonoEmpresa = $data['telefonoEmpresa'] ?? null;
        $razonSocial = $data['razonSocial'] ?? null; 
        $registroFiscal = $data['registroFiscal'] ?? null; 
    
        // Validar datos adicionales para Empresa
        if ($Tipo === 'Empresa') {
            if (empty($direccion) || empty($razonSocial) || empty($registroFiscal)) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Faltan datos de la empresa (dirección, razón social o registro fiscal).'
                ]);
            }
        }
        
        // ID del usuario que se creará
        $p_id_usuario = null;
    
        // Llamar al método add_usuario de la clase usuario sin pasar el ID por referencia
        $result = usuario::addusuario($email, $contraseña, $rol, $Tipo, $nombre, $apellido, $dni, $edad, $telefono, $nombreEmpresa, $direccion, $telefonoEmpresa, $razonSocial, $registroFiscal, $p_id_usuario);
    
        if ($result) {
            return json_encode([
                'status' => 'success',
                'message' => 'Usuario creado correctamente.',
                'id_usuario' => $p_id_usuario
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear el usuario.'
            ]);
        }
    }
}
