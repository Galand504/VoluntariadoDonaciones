<?php
namespace App\Model;

class riesgo
{
    private $idRiesgo;
    private $descripcion;
    private $planMitigacion;
    private $idProyecto;

    public function __construct($descripcion, $planMitigacion, $idProyecto, $idRiesgo = null)
    {
        $this->idRiesgo = $idRiesgo;
        $this->descripcion = $descripcion;
        $this->planMitigacion = $planMitigacion;
        $this->idProyecto = $idProyecto;
    }

    public function getIdRiesgo() { return $this->idRiesgo; }
    public function getDescripcion() { return $this->descripcion; }
    public function getPlanMitigacion() { return $this->planMitigacion; }
    public function getIdProyecto() { return $this->idProyecto; }
}
