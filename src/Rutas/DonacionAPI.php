<?php
header("Content-Type: application/json; charset=UTF-8");

// Encabezados CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejo de preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

// Incluir clases necesarias
include_once 'Database.php';
include_once 'Donacion.php';
include_once 'Pago.php';

// Inicializar conexión
$database = new Database();
$db = $database->getConnection();

// Instanciar objetos de Donacion y Pago
$donacion = new Donacion($db);
$pago = new Pago($db);

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];
// Obtener el ID de la donación de la URL, si está presente
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    switch ($method) {
        case 'POST':
            // Leer los datos enviados en la solicitud
            $data = json_decode(file_get_contents("php://input"));

            // Validar si los datos de la donación son completos
            if (!empty($data->monto) && !empty($data->fecha) && !empty($data->idUsuario) && !empty($data->idProyecto)) {
                // Asignar los datos a la clase Donacion
                $donacion->monto = htmlspecialchars(strip_tags($data->monto));
                $donacion->fecha = htmlspecialchars(strip_tags($data->fecha));
                $donacion->id_usuario = intval($data->idUsuario);
                $donacion->idProyecto = intval($data->idProyecto);

                // Crear donación y verificar éxito
                if ($donacion->createDonacion()) {
                    // Obtener el último ID insertado de la donación
                    $donacion->idDonacion = $db->lastInsertId();

                    // Registrar el pago solo si los datos están completos
                    if (!empty($data->montoPago) && !empty($data->fechaPago) && !empty($data->metodoPago)) {
                        $pago->idDonacion = $donacion->idDonacion;
                        $pago->montoPago = htmlspecialchars(strip_tags($data->montoPago));
                        $pago->fechaPago = htmlspecialchars(strip_tags($data->fechaPago));
                        $pago->metodoPago = htmlspecialchars(strip_tags($data->metodoPago));

                        if ($pago->crearPago()) {
                            echo json_encode(["status" => "success", "message" => "Donación y pago registrados exitosamente."]);
                        } else {
                            throw new Exception("Donación creada, pero error al registrar el pago.");
                        }
                    } else {
                        echo json_encode(["status" => "success", "message" => "Donación registrada, pero datos de pago incompletos."]);
                    }
                } else {
                    throw new Exception("Error al crear la donación.");
                }
            } else {
                throw new Exception("Datos incompletos para la donación.");
            }
            break;

        case 'GET':
            if ($id) {
                // Obtener una donación específica
                $donacion->idDonacion = $id;
                if ($donacion->obtenerDonacion()) {
                    // Obtener detalle del pago asociado
                    $pago->idDonacion = $id;
                    $pagoData = $pago->obtenerPago();

                    $response = [
                        "idDonacion" => $donacion->idDonacion,
                        "monto" => $donacion->monto,
                        "fecha" => $donacion->fecha,
                        "idUsuario" => $donacion->id_usuario,
                        "idProyecto" => $donacion->idProyecto,
                        "pago" => $pagoData
                    ];

                    echo json_encode(["status" => "success", "data" => $response]);
                } else {
                    throw new Exception("Donación no encontrada.");
                }
            } else {
                // Obtener todas las donaciones
                $stmt = $donacion->obtenerDonaciones();
                $donaciones_arr = [];

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $donacion_item = [
                        "idDonacion" => $row['idDonacion'],
                        "monto" => $row['monto'],
                        "fecha" => $row['fecha'],
                        "idUsuario" => $row['id_usuario'],
                        "idProyecto" => $row['idProyecto'],
                        "pago" => $pago->obtenerPago($row['idDonacion'])
                    ];
                    $donaciones_arr[] = $donacion_item;
                }

                echo json_encode(["status" => "success", "data" => $donaciones_arr]);
            }
            break;

        default:
            throw new Exception("Método HTTP no soportado.");
    }
} catch (Exception $e) {
    // Manejo de excepciones
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

