<?php
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

include_once 'Database.php';
include_once 'Donacion.php';
include_once 'Pago.php';

$database = new Database();
$db = $database->getConnection();

$donacion = new Donacion($db);
$pago = new Pago($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents("php://input"));

            if (!empty($data->monto) && !empty($data->fecha) && !empty($data->idUsuario) && !empty($data->idProyecto)) {
                $donacion->monto = htmlspecialchars(strip_tags($data->monto));
                $donacion->fecha = htmlspecialchars(strip_tags($data->fecha));
                $donacion->id_usuario = intval($data->idUsuario);
                $donacion->idProyecto = intval($data->idProyecto);

                if ($donacion->createDonacion()) {
                    $donacion->idDonacion = $db->lastInsertId();

                    if (!empty($data->montoPago) && !empty($data->fechaPago) && !empty($data->metodoPago)) {
                        $pago->idDonacion = $donacion->idDonacion;
                        $pago->montoPago = htmlspecialchars(strip_tags($data->montoPago));
                        $pago->fechaPago = htmlspecialchars(strip_tags($data->fechaPago));
                        $pago->metodoPago = htmlspecialchars(strip_tags($data->metodoPago));

                        if ($pago->crearPago()) {
                            echo json_encode(["status" => "success", "message" => "Donación y pago registrados."]);
                        } else {
                            throw new Exception("Error al registrar el pago.");
                        }
                    } else {
                        echo json_encode(["status" => "success", "message" => "Donación registrada, datos de pago incompletos."]);
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
                $donacion->idDonacion = $id;
                if ($donacion->obtenerDonacion()) {
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
                $stmt = $donacion->obtenerDonaciones();
                $donaciones = [];

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $donaciones[] = [
                        "idDonacion" => $row['idDonacion'],
                        "monto" => $row['monto'],
                        "fecha" => $row['fecha'],
                        "idUsuario" => $row['id_usuario'],
                        "idProyecto" => $row['idProyecto'],
                        "pago" => $pago->obtenerPago($row['idDonacion'])
                    ];
                }

                echo json_encode(["status" => "success", "data" => $donaciones]);
            }
            break;

        default:
            throw new Exception("Método HTTP no soportado.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

