<?php

namespace App\Controladores;

use App\Configuracion\Donacion;
use App\Database\DatabaseConnection;

class DonacionController
{
    private $donacion;

    public function __construct()
    src/Database/DatabaseConnection.php
    {
        // Crear conexión a la base de datos
        $dbConnection = new DatabaseConnection();
        
        $db = $dbConnection->getConnection();

        // Instanciar la clase Donacion
        $this->donacion = new Donacion($db);
    }

    // Crear una nueva donación
    public function crearDonacion($monto, $fecha, $id_usuario, $idProyecto): bool
    {
        $this->donacion->monto = $monto;
        $this->donacion->fecha = $fecha;
        $this->donacion->id_usuario = $id_usuario;
        $this->donacion->idProyecto = $idProyecto;

        return $this->donacion->createDonacion();
    }

    // Obtener todas las donaciones
    public function obtenerDonaciones(): array
    {
        return $this->donacion->obtenerDonaciones();
    }

    // Obtener una donación específica por ID
    public function obtenerDonacion($idDonacion): ?array
    {
        $this->donacion->idDonacion = $idDonacion;

        if ($this->donacion->obtenerDonacion()) {
            return [
                "idDonacion" => $this->donacion->idDonacion,
                "monto" => $this->donacion->monto,
                "fecha" => $this->donacion->fecha,
                "id_usuario" => $this->donacion->id_usuario,
                "idProyecto" => $this->donacion->idProyecto
            ];
        }
        return null;
    }

    // Actualizar una donación
    public function actualizarDonacion($idDonacion, $monto, $fecha, $id_usuario, $idProyecto): bool
    {
        $this->donacion->idDonacion = $idDonacion;
        $this->donacion->monto = $monto;
        $this->donacion->fecha = $fecha;
        $this->donacion->id_usuario = $id_usuario;
        $this->donacion->idProyecto = $idProyecto;

        return $this->donacion->actualizarDonacion();
    }

    // Eliminar una donación
    public function eliminarDonacion($idDonacion): bool
    {
        $this->donacion->idDonacion = $idDonacion;

        return $this->donacion->eliminarDonacion();
    }
}
