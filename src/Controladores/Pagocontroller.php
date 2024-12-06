<?php

class PagoController {
    private $db;

    // Constructor que recibe la conexión a la base de datos
    public function __construct($db) {
        $this->db = $db;
    }

    // Crear un nuevo pago
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderMetodoNoPermitido();
            return;
        }

        // Leer datos enviados en la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        $camposRequeridos = ['monto', 'estado', 'idDonacion', 'id_metodopago', 'moneda'];

        if (!$this->validarEntrada($data, $camposRequeridos)) {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos o inválidos."]);
            return;
        }

        // Crear instancia del modelo y asignar valores
        $pago = new Pago($this->db);
        $pago->fecha = $data['fecha'] ?? date('Y-m-d H:i:s'); // Fecha actual si no se proporciona
        $pago->monto = $data['monto'];
        $pago->estado = $data['estado'];
        $pago->idDonacion = $data['idDonacion'];
        $pago->id_metodopago = $data['id_metodopago'];
        $pago->referencia_externa = $data['referencia_externa'] ?? null;
        $pago->moneda = $data['moneda'];

        try {
            if ($pago->crearPago()) {
                http_response_code(201);
                echo json_encode(["message" => "Pago creado con éxito.", "idPago" => $pago->idPago]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error al crear el pago."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error interno del servidor.", "error" => $e->getMessage()]);
        }
    }

    // Listar pagos
    public function listar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responderMetodoNoPermitido();
            return;
        }

        try {
            $filtro = $_GET;
            $pago = new Pago($this->db);
            $resultados = $pago->obtenerPagos($filtro);

            http_response_code(200);
            echo json_encode($resultados);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error al obtener los pagos.", "error" => $e->getMessage()]);
        }
    }

    // Actualizar un pago
    public function actualizar($idPago) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->responderMetodoNoPermitido();
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($idPago)) {
            http_response_code(400);
            echo json_encode(["message" => "Se requiere un ID válido para actualizar."]);
            return;
        }

        // Instanciar modelo
        $pago = new Pago($this->db);
        $pago->idPago = $idPago;

        // Asignar datos
        $pago->fecha = $data['fecha'] ?? null;
        $pago->monto = $data['monto'] ?? null;
        $pago->estado = $data['estado'] ?? null;
        $pago->idDonacion = $data['idDonacion'] ?? null;
        $pago->id_metodopago = $data['id_metodopago'] ?? null;
        $pago->referencia_externa = $data['referencia_externa'] ?? null;
        $pago->moneda = $data['moneda'] ?? null;

        try {
            if ($pago->actualizarPago()) {
                http_response_code(200);
                echo json_encode(["message" => "Pago actualizado con éxito."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "No se pudo actualizar el pago. Verifica los datos enviados."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error al actualizar el pago.", "error" => $e->getMessage()]);
        }
    }

    // Eliminar un pago (lógica de eliminación suave)
    public function eliminar($idPago) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responderMetodoNoPermitido();
            return;
        }

        if (empty($idPago)) {
            http_response_code(400);
            echo json_encode(["message" => "Se requiere un ID válido para eliminar."]);
            return;
        }

        $pago = new Pago($this->db);
        $pago->idPago = $idPago;

        try {
            if ($pago->eliminarPago()) {
                http_response_code(200);
                echo json_encode(["message" => "Pago eliminado correctamente."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Pago no encontrado o no se pudo eliminar."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error al eliminar el pago.", "error" => $e->getMessage()]);
        }
    }

    // Método para responder con 405 si el método no es permitido
    private function responderMetodoNoPermitido() {
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido."]);
    }

    // Método para validar la entrada de datos
    private function validarEntrada($data, $camposRequeridos) {
        foreach ($camposRequeridos as $campo) {
            if (!isset($data[$campo]) || empty($data[$campo])) {
                return false;
            }
        }
        return true;
    }
}



