<?php
require_once __DIR__ . '/../config/database.php';

class Voiture
{
      private $conn;
      private $table = 'voiture';
      private $lastError = '';

      public function __construct($conn)
      {
            $this->conn = $conn;
      }

      public function create($idvoit, $design, $codeit, $frais)
      {
            try {
                  $stmt = $this->conn->prepare("INSERT INTO {$this->table} (idvoit, design, codeit, frais) VALUES (?, ?, ?, ?)");
                  $stmt->bind_param("sssi", $idvoit, $design, $codeit, $frais);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible d'ajouter cette voiture. Vérifiez l'ID et le code itinéraire.";
                  return false;
            }
      }

      public function getAll()
      {
            return $this->conn->query("SELECT * FROM {$this->table} ORDER BY idvoit");
      }

      public function getById($idvoit)
      {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE idvoit = ?");
            $stmt->bind_param("s", $idvoit);
            $stmt->execute();
            return $stmt->get_result();
      }

      public function update($idvoit, $design, $codeit, $frais)
      {
            try {
                  $stmt = $this->conn->prepare("UPDATE {$this->table} SET design = ?, codeit = ?, frais = ? WHERE idvoit = ?");
                  $stmt->bind_param("ssis", $design, $codeit, $frais, $idvoit);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible de modifier cette voiture. Vérifiez le code itinéraire.";
                  return false;
            }
      }

      public function delete($idvoit)
      {
            try {
                  if ($this->countEnvois($idvoit) > 0) {
                        $this->lastError = "Impossible de supprimer cette voiture : elle est utilisée par un ou plusieurs envois.";
                        return false;
                  }

                  $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE idvoit = ?");
                  $stmt->bind_param("s", $idvoit);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible de supprimer cette voiture.";
                  return false;
            }
      }

      private function countEnvois($idvoit)
      {
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM envoyer WHERE idvoit = ?");
            $stmt->bind_param("s", $idvoit);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            return (int) $row['total'];
      }

      public function getLastError()
      {
            return $this->lastError;
      }
}
