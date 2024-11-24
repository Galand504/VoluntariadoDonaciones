<?php

class recompensa {
    private $donaciones;
    private $donadorEstrella;
    private $criterioCantidad = 5; // Número mínimo de donaciones para ser donador estrella
    private $criterioMonto = 1000; // Monto mínimo para ser donador estrella

    public function __construct($donaciones) {
        $this->donaciones = $donaciones;
        $this->donadorEstrella = $this->determinarDonadorEstrella();
    }

    private function determinarDonadorEstrella() {
        $donadores = [];
        foreach ($this->donaciones as $donacion) {
            $donador = $donacion->getDonador();
            $monto = $donacion->getMonto();

            if (!isset($donadores[$donador])) {
                $donadores[$donador] = ['cantidad' => 0, 'total' => 0];
            }
            $donadores[$donador]['cantidad']++;
            $donadores[$donador]['total'] += $monto;
        }

        foreach ($donadores as $donador => $datos) {
            if ($datos['cantidad'] >= $this->criterioCantidad || $datos['total'] >= $this->criterioMonto) {
                return $donador;
            }
        }

        return null;
    }

    public function mostrarFormulario() {
        if ($this->donadorEstrella) {
            echo '<form action="recompensa.php" method="post">';
            echo '<h2>Donador Estrella</h2>';
            echo '<p>Felicidades, ' . htmlspecialchars($this->donadorEstrella) . '! Eres merecedor de una recompensa.</p>';
            echo '<input type="hidden" name="donador" value="' . htmlspecialchars($this->donadorEstrella) . '">';
            echo '<button type="submit">Reclamar Recompensa</button>';
            echo '</form>';
        } else {
            echo '<p>No hay donadores estrella en este momento.</p>';
        }
    }
}

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

