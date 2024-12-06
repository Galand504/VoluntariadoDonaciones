<?php

namespace App\clases;

use App\clases\donacion;
use App\clases\pago;
use PDO;

class recompensa{
    private $db;
    private $donacionModel;
    private $pagoModel;
    private $donadorEstrella;
    private $criterioCantidad = 5; // Número mínimo de donaciones
    private $criterioMonto = 1000; // Monto mínimo total

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->donacionModel = new donacion($db);
        $this->pagoModel = new pago($db);
        $this->donadorEstrella = $this->determinarDonadorEstrella();
    }

    private function determinarDonadorEstrella() {
        $stmtDonaciones = $this->donacionModel->obtenerDonaciones();
        $donadores = [];

        while ($donacion = $stmtDonaciones->fetch(PDO::FETCH_ASSOC)) {
            $idDonacion = $donacion['idDonacion'];
            $idUsuario = $donacion['id_usuario'];
            $montoDonacion = $donacion['monto'];

            // Validar los pagos asociados a la donación
            $pagos = $this->pagoModel->obtenerPagos(['idDonacion' => $idDonacion, 'estado' => 'Completado']);
            $montoPagosValidados = 0;

            foreach ($pagos as $pago) {
                $montoPagosValidados += $pago['monto'];
            }

            // Solo procesar donaciones con pagos válidos
            if ($montoPagosValidados >= $montoDonacion) {
                if (!isset($donadores[$idUsuario])) {
                    $donadores[$idUsuario] = ['cantidad' => 0, 'total' => 0];
                }
                $donadores[$idUsuario]['cantidad']++;
                $donadores[$idUsuario]['total'] += $montoDonacion;
            }
        }

        // Verificar los criterios
        foreach ($donadores as $donador => $datos) {
            if ($datos['cantidad'] >= $this->criterioCantidad || $datos['total'] >= $this->criterioMonto) {
                return $donador; // Devuelve el primer donador que cumple los criterios
            }
        }

        return null; // No hay donadores estrella
    }

    public function mostrarFormulario() {
        if ($this->donadorEstrella) {
            echo '<form action="recompensa.php" method="post">';
            echo '<h2>Donador Estrella</h2>';
            echo '<p>Felicidades, Donador ID: ' . htmlspecialchars($this->donadorEstrella) . '! Eres merecedor de una recompensa.</p>';
            echo '<input type="hidden" name="donador" value="' . htmlspecialchars($this->donadorEstrella) . '">';
            echo '<button type="submit">Reclamar Recompensa</button>';
            echo '</form>';
        } else {
            echo '<p>No hay donadores estrella en este momento.</p>';
        }
    }
}
?>


/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

