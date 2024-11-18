<?php
namespace App\clases;

use App\Base\Database;
use App\Configuracion\Security;
use PDO;

class usuario {

    // Ejecuta el procedimiento almacenado AddUsuario
    public static function addusuario($email, $contraseña, $rol, $Tipo, $nombre, $apellido, $dni, $edad, $telefono, $nombreEmpresa = null, $direccion = null, $telefonoEmpresa = null, $razonSocial = null, $registroFiscal = null, &$p_id_usuario) {
        $con = Database::getConnection(); // Llamamos al Singleton para obtener la conexión
    
        // Hash de la contraseña utilizando Security
        $hashed_password = Security::createPassword($contraseña);
    
        // Preparar y ejecutar el procedimiento almacenado
        $stmt = $con->prepare("CALL AddUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @p_id_usuario)");
        $stmt->execute([
            $email,
            $hashed_password, // Se utiliza la contraseña hasheada
            $rol,
            $Tipo,
            $nombre,
            $apellido,
            $dni,
            $edad,
            $telefono,
            $nombreEmpresa ?? null,
            $telefonoEmpresa ?? null,
            $direccion ?? null, // Si no se pasa, se asigna null
            $razonSocial ?? null, // Si no se pasa, se asigna null
            $registroFiscal ?? null // Si no se pasa, se asigna null
        ]);
    
        // Obtenemos el ID del usuario insertado
        $result = $con->query("SELECT @p_id_usuario AS id_usuario")->fetch(PDO::FETCH_ASSOC);
        $p_id_usuario = $result['id_usuario']; // Asignamos el ID del nuevo usuario
    
        return $p_id_usuario ? true : false; // Retorna true si la operación fue exitosa, false de lo contrario
    }
         // Ejecuta el procedimiento almacenado UpdateUsuario
        public static function updateusuario($idUsuario, $nombre,  $apellido, $email, $contraseña, $telefono, $dni, $edad, $Rol, $tipo, $nombreEmpresa = null,
        $razonSocial = null, $telefonoEmpresa = null, $direccion = null, $registroFiscal = null) {
            $con = Database::getConnection(); // Llamamos al Singleton para obtener la conexión
    
            // Hash de la contraseña utilizando Security
            $hashed_password = Security::createPassword($contraseña);
    
            // Preparar y ejecutar el procedimiento almacenado
            $stmt = $con->prepare("CALL UpdateUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $idUsuario,
                $nombre,
                $apellido,
                $email,
                $hashed_password, // Contraseña hasheada
                $telefono,
                $dni,
                $edad,
                $Rol,
                $tipo,
                $nombreEmpresa ?? null,
                $telefonoEmpresa ?? null,   
                $razonSocial ?? null,
                $direccion ?? null,
                $registroFiscal ?? null,
            ]);
    
            // Comprobar si se actualizó correctamente
            return $stmt->rowCount() > 0;
        }
    }
    

    

