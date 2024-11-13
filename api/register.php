<?php
header('Content-Type: application/json');

// Conectar a la base de datos
$dsn = 'mysql:host=localhost;dbname=voluntariadodonaciones';
$username = 'root';  // Cambia esto según tu configuración
$password = '';  // Cambia esto según tu configuración

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si la solicitud es de tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos enviados en formato JSON
        $data = json_decode(file_get_contents("php://input"));

        // Verifica que los campos necesarios estén presentes
        if (isset($data->nombre) && isset($data->email) && isset($data->contraseña) && isset($data->tipoUsuario)) {
            $nombre = $data->nombre;
            $email = $data->email;
            $contraseña = password_hash($data->contraseña, PASSWORD_BCRYPT);
            $tipoUsuario = $data->tipoUsuario;

            // Insertar en la tabla usuario
            $sql = "INSERT INTO usuario (nombre, email, contraseña, tipoUsuario) VALUES (:nombre, :email, :contraseña, :tipoUsuario)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contraseña', $contraseña);
            $stmt->bindParam(':tipoUsuario', $tipoUsuario);

            if ($stmt->execute()) {
                $idUsuario = $db->lastInsertId(); // Obtener el id del usuario recién insertado

                // Registrar dependiendo del tipo de usuario
                if ($tipoUsuario === 'persona') {
                    // Datos adicionales para la persona
                    $dni = $data->dni;
                    $apellido = $data->apellido;
                    $edad = $data->edad;
                    $telefono = $data->telefono;

                    // Insertar en la tabla persona
                    $sqlPersona = "INSERT INTO persona (DNI, nombre, apellido, edad, telefono, idUsuario) VALUES (:dni, :nombre, :apellido, :edad, :telefono, :idUsuario)";
                    $stmtPersona = $db->prepare($sqlPersona);
                    $stmtPersona->bindParam(':dni', $dni);
                    $stmtPersona->bindParam(':nombre', $nombre);
                    $stmtPersona->bindParam(':apellido', $apellido);
                    $stmtPersona->bindParam(':edad', $edad);
                    $stmtPersona->bindParam(':telefono', $telefono);
                    $stmtPersona->bindParam(':idUsuario', $idUsuario);

                    if ($stmtPersona->execute()) {
                        echo json_encode(['message' => 'Usuario y persona registrados exitosamente']);
                    } else {
                        echo json_encode(['message' => 'Error al registrar la persona']);
                    }
                } elseif ($tipoUsuario === 'empresa') {
                    // Datos adicionales para la empresa
                    $direccion = $data->direccion;
                    $telefono = $data->telefono;

                    // Insertar en la tabla empresa
                    $sqlEmpresa = "INSERT INTO empresa (direccion, telefono, idUsuario) VALUES (:direccion, :telefono, :idUsuario)";
                    $stmtEmpresa = $db->prepare($sqlEmpresa);
                    $stmtEmpresa->bindParam(':direccion', $direccion);
                    $stmtEmpresa->bindParam(':telefono', $telefono);
                    $stmtEmpresa->bindParam(':idUsuario', $idUsuario);

                    if ($stmtEmpresa->execute()) {
                        echo json_encode(['message' => 'Usuario y empresa registrados exitosamente']);
                    } else {
                        echo json_encode(['message' => 'Error al registrar la empresa']);
                    }
                } else {
                    echo json_encode(['message' => 'Tipo de usuario no válido']);
                }
            } else {
                echo json_encode(['message' => 'Error al registrar usuario']);
            }
        } else {
            echo json_encode(['error' => 'Faltan campos necesarios']);
        }
    } else {
        // Si no es una solicitud POST
        echo json_encode(['error' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
}
?>


