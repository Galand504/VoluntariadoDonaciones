<?php
namespace App\Controller;

use App\Database\DatabaseConnection;
use App\Model\Riesgo;
use PDO;

class RiesgoController
{
    private $db;

    public function __construct()
    src/Database/DatabaseConnection.php

    {
        $database = new DatabaseConnection();
        
        $this->db = $database->getConnection();
    }

    public function create(Riesgo $riesgo)
    {
        $query = "INSERT INTO riesgo (descripcion, planMitigacion, idProyecto) VALUES (:descripcion, :planMitigacion, :idProyecto)";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':descripcion', $riesgo->getDescripcion());
        $stmt->bindValue(':planMitigacion', $riesgo->getPlanMitigacion());
        $stmt->bindValue(':idProyecto', $riesgo->getIdProyecto());

        return $stmt->execute();
    }

    public function read($idRiesgo)
    {
        $query = "SELECT * FROM riesgo WHERE idRiesgo = :idRiesgo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idRiesgo', $idRiesgo);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(Riesgo $riesgo)
    {
        $query = "UPDATE riesgo SET descripcion = :descripcion, planMitigacion = :planMitigacion WHERE idRiesgo = :idRiesgo";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':descripcion', $riesgo->getDescripcion());
        $stmt->bindValue(':planMitigacion', $riesgo->getPlanMitigacion());
        $stmt->bindValue(':idRiesgo', $riesgo->getIdRiesgo());

        return $stmt->execute();
    }

    public function delete($idRiesgo)
    {
        $query = "DELETE FROM riesgo WHERE idRiesgo = :idRiesgo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idRiesgo', $idRiesgo);

        return $stmt->execute();
    }
}
