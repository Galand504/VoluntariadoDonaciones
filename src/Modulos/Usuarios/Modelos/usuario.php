<?php
namespace App\Modulos\Usuarios\Modelos;

use App\Base\Database;
use App\Configuracion\Security;
use PDO;
use PDOException;

class usuario {
    public static function emailExists($email)
        {
            try {
                $conexion = Database::getConnection();
                $query = "SELECT COUNT(*) FROM usuario WHERE email = :email";
                $stmt = $conexion->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                
                return $stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                // Manejar el error de manera apropiada
                error_log("Error al verificar email: " . $e->getMessage());
                return false;
            }
        }

        public static function login($email, $contraseña) {
            try {
                $con = Database::getConnection();
                
                // Primero obtener el usuario
                $stmt = $con->prepare("SELECT * FROM usuario WHERE email = ?");
                $stmt->execute([$email]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verificar si existe el usuario y la contraseña es correcta
                if ($result && password_verify($contraseña, $result['contraseña'])) {
                    // Preparar datos para el token
                    $userData = [
                        'id' => $result['id_usuario'],
                        'email' => $result['email'],
                        'rol' => $result['rol']
                    ];
                    
                    // Generar token
                    $token = Security::createTokenJwt(Security::secretKey(), $userData);
                    
                    return [
                        'status' => true,
                        'token' => $token,
                        'usuario' => $userData
                    ];
                }
                
                return [
                    'status' => false,
                    'message' => 'Credenciales inválidas'
                ];
                
            } catch (PDOException $e) {
                error_log("Error en login: " . $e->getMessage());
                return [
                    'status' => false,
                    'message' => 'Error en el inicio de sesión'
                ];
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
         public static function updateUsuario($id_usuario, $nombre, $apellido, $email, $contraseña, $telefono, $dni, $edad,  &$p_id_usuario, $rol = null, $tipo = null, $nombreEmpresa = null, $razonSocial = null, $telefonoEmpresa = null, $direccion = null, $registroFiscal = null): bool {
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
       