<?php
namespace App\Configuracion;


require_once __DIR__ . '/../pago.php';  
require_once __DIR__ . '/../donacion.php'; 

use App\Configuracion\donacion;
use App\Configuracion\Pago;
use ComposerAutoloaderInitd751713988987e9331980363e24189ce;
use PDO;
use php_user_filter;

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
        $this->pagoModel = new Pago($db);
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
            echo '<div class="container">';
            echo '<div class="card">';
            echo '<h2>Donador Estrella</h2>';
            echo '<p>Felicidades, Donador ID: <span id="donador-id">' . htmlspecialchars($this->donadorEstrella) . '</span>! Eres merecedor de una recompensa.</p>';
            echo '<form action="recompensa.php" method="post">';
            echo '<input type="hidden" name="donador" value="' . htmlspecialchars($this->donadorEstrella) . '">';
            echo '<button type="submit" class="btn">Reclamar Recompensa</button>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="container">';
            echo '<p id="no-donador">No hay donadores estrella en este momento.</p>';
            echo '</div>';
        }
    }
    
}

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

