<?php

namespace App\Controladores;

use App\Configuracion\Donacion;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use App\Base\Database;

class DonacionController
{
    private $donacion;

    public function __construct()
    {
        // Crear conexión a la base de datos
        $dbConnection = new DatabaseConnection();
        $db = $dbConnection->getConnection();
        $this->donacion = new Donacion($db);
    }

    public function crearDonacion($monto, $fecha, $id_usuario, $idProyecto): array
    {
        // Verificar el token de seguridad
        if (!Security::validateTokenJwt(Security::secretKey())) {
            return ResponseHTTP::status401('Acceso no autorizado.');
        }

        $this->donacion->monto = $monto;
        $this->donacion->fecha = $fecha;
        $this->donacion->id_usuario = $id_usuario;
        $this->donacion->idProyecto = $idProyecto;

        if ($this->donacion->createDonacion()) {
            return [
                'status' => 200,
                'message' => 'Donación creada exitosamente'
            ];
        }
        return ResponseHTTP::status400('Error al crear la donación.');
    }

    public function obtenerDonaciones(): array
    {
        // Verificar el token de seguridad
        if (!Security::validateTokenJwt(Security::secretKey())) {
            return ResponseHTTP::status401('Acceso no autorizado.');
        }

        $result = $this->donacion->obtenerDonaciones();
        if ($result) {
            return [
                'status' => 200,
                'data' => $result
            ];
        }
        return ResponseHTTP::status400('No se pudieron obtener las donaciones.');
    }

    public function obtenerDonacion($idDonacion): array
    {
        // Verificar el token de seguridad
        if (!Security::validateTokenJwt(Security::secretKey())) {
            return ResponseHTTP::status401('Acceso no autorizado.');
        }

        $this->donacion->idDonacion = $idDonacion;
        $result = $this->donacion->obtenerDonacion();

        if ($result) {
            return [
                'status' => 200,
                'data' => [
                    "idDonacion" => $this->donacion->idDonacion,
                    "monto" => $this->donacion->monto,
                    "fecha" => $this->donacion->fecha,
                    "id_usuario" => $this->donacion->id_usuario,
                    "idProyecto" => $this->donacion->idProyecto
                ]
            ];
        }
        return ResponseHTTP::status400('No se encontró la donación.');
    }

    public function actualizarDonacion($idDonacion, $monto, $fecha, $id_usuario, $idProyecto): array
    {
        // Verificar el token de seguridad
        if (!Security::validateTokenJwt(Security::secretKey())) {
            return ResponseHTTP::status401('Acceso no autorizado.');
        }

        $this->donacion->idDonacion = $idDonacion;
        $this->donacion->monto = $monto;
        $this->donacion->fecha = $fecha;
        $this->donacion->id_usuario = $id_usuario;
        $this->donacion->idProyecto = $idProyecto;

        if ($this->donacion->actualizarDonacion()) {
            return [
                'status' => 200,
                'message' => 'Donación actualizada exitosamente'
            ];
        }
        return ResponseHTTP::status400('Error al actualizar la donación.');
    }

    public function eliminarDonacion($idDonacion): array
    {
        // Verificar el token de seguridad
        if (!Security::validateTokenJwt(Security::secretKey())) {
            return ResponseHTTP::status401('Acceso no autorizado.');
        }

        $this->donacion->idDonacion = $idDonacion;

        if ($this->donacion->eliminarDonacion()) {
            return [
                'status' => 200,
                'message' => 'Donación eliminada exitosamente'
            ];
        }
        return ResponseHTTP::status400('Error al eliminar la donación.');
    }
}
