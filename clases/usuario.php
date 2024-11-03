<?php
class Usuario {
    private $db;

    public function __construct($db) {
        $this->db = $db; // Conexión a la base de datos
    }

    public function registrar($nombre, $email, $contraseña) {
        // Lógica para registrar un nuevo usuario
        $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (nombre, email, contraseña) VALUES (:nombre, :email, :contraseña)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contraseña', $hashedPassword);
        return $stmt->execute();
    }

    public function login($email, $contraseña) {
        // Lógica para iniciar sesión
        $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($contraseña, $usuario['contraseña'])) {
                // El inicio de sesión fue exitoso
                return $usuario; // Devuelve la información del usuario
            }
        }
        return false; // El inicio de sesión falló
    }
}
?>
