<?php
namespace App\clases;

use App\Base\Database;
use App\Configuracion\Security;
use PDO;
use PDOException;

class usuario {

    public static function getUsuarioByEmail($email) {
        // Obtener la conexión a la base de datos
        $con = Database::getConnection();
    
        try {
            // Preparar la consulta para obtener el usuario, incluyendo el campo 'tipo'
            $stmt = $con->prepare("SELECT id_usuario, email, rol, contraseña, tipo FROM usuario WHERE email = ?");
    
            // Ejecutar la consulta
            $stmt->execute([$email]);
    
            // Obtener el resultado
            $usuario = $stmt->fetch();
    
            // Si no se encuentra el usuario, devolver null
            if (!$usuario) {
                return null;
            }
    
            // Si el usuario existe, devolver los datos incluyendo el tipo (persona o empresa)
            return $usuario;
    
        } catch (PDOException $e) {
            // Manejo de excepciones y error logging
            error_log("Error al obtener el usuario: " . $e->getMessage());
            return null;
        }
        
    }
    


    // Ejecuta el procedimiento almacenado AddUsuario
    public static function addusuario($email, $contraseña, $rol, $Tipo, $nombre, $apellido, $dni, $edad, $telefono, &$p_id_usuario, $nombreEmpresa = null, $direccion = null, $telefonoEmpresa = null, $razonSocial = null, $registroFiscal = null) {
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
            $nombre ?? null,
            $apellido ?? null,
            $dni ?? null,
            $edad ?? null,
            $telefono ?? null,
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
         public static function updateUsuario($id_usuario, $nombre, $apellido, $email, $contraseña, $telefono, $dni, $edad,  &$p_id_usuario, $rol = null, $tipo = null, $nombreEmpresa = null, $razonSocial = null, $telefonoEmpresa = null, $direccion = null, $registroFiscal = null) {
            $con = Database::getConnection(); // Llamamos al Singleton para obtener la conexión
        
            // Hash de la contraseña utilizando Security
            $hashed_password = Security::createPassword($contraseña);
        
            // Preparar y ejecutar el procedimiento almacenado
            $stmt = $con->prepare("CALL UpdateUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $id_usuario, // Este valor no se modifica
                $nombre ?? null,
                $apellido ?? null,
                $email,
                $hashed_password, // Contraseña hasheada
                $telefono ?? null,
                $dni ?? null,
                $edad ?? null,
                $rol ?? null,
                $tipo ?? null,
                $nombreEmpresa ?? null,
                $telefonoEmpresa ?? null,
                $razonSocial ?? null,
                $direccion ?? null,
                $registroFiscal ?? null,
            ]);
        
            // Comprobar si se actualizó correctamente
            return $stmt->rowCount() > 0;
        }
        
        
        public static function deleteUsuario($id_usuario) {
            $con = Database::getConnection(); // Llamamos al Singleton para obtener la conexión
        
            try {
                // Preparar y ejecutar el procedimiento almacenado
                $stmt = $con->prepare("CALL DeleteUsuario(?)");
                $stmt->execute([$id_usuario]);
        
                // Devolver true si la operación fue exitosa
                return true;
            } catch (PDOException $e) {
                error_log("Error en deleteUsuario: " . $e->getMessage());
                return false;
            }
        }
        
        public static function getAllUsuarios() {
            $con = Database::getConnection();
            
            // Obtener todas las personas
            $stmtPersona = $con->prepare("SELECT * FROM persona p INNER JOIN usuario u ON p.id_usuario = u.id_usuario");
            $stmtPersona->execute();
            $personas = $stmtPersona->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener todas las empresas
            $stmtEmpresa = $con->prepare("SELECT * FROM empresa e INNER JOIN usuario u ON e.id_usuario = u.id_usuario");
            $stmtEmpresa->execute();
            $empresas = $stmtEmpresa->fetchAll(PDO::FETCH_ASSOC);
            
            // Combinar las personas y las empresas en un solo array
            return [
                'persona' => $personas,
                'empresa' => $empresas
            ];
        }  
         
        
        public static function getUsuarioById($idUsuario) {
            $con = Database::getConnection(); // Obtener la conexión
            try {
                $query = "CALL GetUsuarioById(:idUsuario)";
                $stmt = $con->prepare($query);
                $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
                $stmt->execute();
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
                return $usuario; // Retornar el usuario encontrado
            } catch (PDOException $e) {
                error_log("Error en getUsuarioById: " . $e->getMessage());
                return false; // Retornar false en caso de error
            }
        }
    }     
       