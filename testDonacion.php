<?php
// testDonacion.php
require_once 'vendor/autoload.php';

use App\Base\Database;
use App\Configuracion\donacion;

// Obtener la conexión desde Database
$db = Database::getConnection();

// Crear una nueva instancia de la clase Donacion con la conexión
$donacion = new donacion($db);

// Ahora puedes llamar a los métodos de la clase Donacion
$donacion->monto = 100;
$donacion->fecha = '2024-11-16';
$donacion->id_usuario = 1;
$donacion->idProyecto = 2;

// Crear una nueva donación
$donacion->createDonacion();
