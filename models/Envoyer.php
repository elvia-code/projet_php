<?php
require_once __DIR__ . '/../config/database.php';

class Envoyer
{
      private $conn;
      private $table = 'envoyer';
      private $idColumn;
      private $lastError = '';

      public function __construct($conn)
      {
            $this->conn = $conn;
            $this->idColumn = $this->columnExists('idenvoi') ? 'idenvoi' : 'denvoi';
      }

      private function columnExists($column)
      {
            $stmt = $this->conn->prepare(
                  "SELECT COUNT(*) AS total
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?"
            );
            $stmt->bind_param("ss", $this->table, $column);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            return (int) $row['total'] > 0;
      }

      public function getIdColumn()
      {
            return $this->idColumn;
      }

      private function normalizeDateTime($value)
      {
            $value = str_replace('T', ' ', $value);
            return strlen($value) === 16 ? $value . ':00' : $value;
      }

      public function create($idvoit, $colis, $nomEnvoyeur, $emailEnvoyeur, $date_envoi, $frais, $nomRecepteur, $contactRecepteur)
      {
            try {
                  $date_envoi = $this->normalizeDateTime($date_envoi);
                  $stmt = $this->conn->prepare(
                        "INSERT INTO {$this->table} (idvoit, colis, nomEnvoyeur, emailEnvoyeur, date_envoi, frais, nomRecepteur, contactRecepteur)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                  );
                  $stmt->bind_param("sssssiss", $idvoit, $colis, $nomEnvoyeur, $emailEnvoyeur, $date_envoi, $frais, $nomRecepteur, $contactRecepteur);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible d'enregistrer cet envoi. Vérifiez que la voiture existe.";
                  return false;
            }
      }

      public function getAll()
      {
            return $this->conn->query("SELECT * FROM {$this->table} ORDER BY {$this->idColumn} DESC");
      }

      public function getById($idenvoi)
      {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE {$this->idColumn} = ?");
            $stmt->bind_param("i", $idenvoi);
            $stmt->execute();
            return $stmt->get_result();
      }

      public function update($idenvoi, $idvoit, $colis, $nomEnvoyeur, $emailEnvoyeur, $date_envoi, $frais, $nomRecepteur, $contactRecepteur)
      {
            try {
                  $date_envoi = $this->normalizeDateTime($date_envoi);
                  $stmt = $this->conn->prepare(
                        "UPDATE {$this->table}
                         SET idvoit = ?, colis = ?, nomEnvoyeur = ?, emailEnvoyeur = ?, date_envoi = ?, frais = ?, nomRecepteur = ?, contactRecepteur = ?
                         WHERE {$this->idColumn} = ?"
                  );
                  $stmt->bind_param("sssssissi", $idvoit, $colis, $nomEnvoyeur, $emailEnvoyeur, $date_envoi, $frais, $nomRecepteur, $contactRecepteur, $idenvoi);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible de modifier cet envoi. Vérifiez que la voiture existe.";
                  return false;
            }
      }

      public function delete($idenvoi)
      {
            try {
                  if ($this->countReceptions($idenvoi) > 0) {
                        $this->lastError = "Impossible de supprimer cet envoi : il est déjà lié à une réception.";
                        return false;
                  }

                  $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE {$this->idColumn} = ?");
                  $stmt->bind_param("i", $idenvoi);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible de supprimer cet envoi.";
                  return false;
            }
      }

      private function countReceptions($idenvoi)
      {
            $recevoirColumn = $this->recevoirEnvoiColumn();
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM recevoir WHERE {$recevoirColumn} = ?");
            $stmt->bind_param("i", $idenvoi);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            return (int) $row['total'];
      }

      private function recevoirEnvoiColumn()
      {
            $table = 'recevoir';
            $column = 'idenvoi';
            $stmt = $this->conn->prepare(
                  "SELECT COUNT(*) AS total
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?"
            );
            $stmt->bind_param("ss", $table, $column);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            return (int) $row['total'] > 0 ? 'idenvoi' : 'denvoi';
      }

      public function getLastError()
      {
            return $this->lastError;
      }

      public function search($keyword)
      {
            $like = '%' . $keyword . '%';
            $stmt = $this->conn->prepare(
                  "SELECT * FROM {$this->table}
                   WHERE CAST({$this->idColumn} AS CHAR) LIKE ?
                   OR colis LIKE ?
                   OR nomEnvoyeur LIKE ?
                   OR nomRecepteur LIKE ?
                   ORDER BY {$this->idColumn} DESC"
            );
            $stmt->bind_param("ssss", $like, $like, $like, $like);
            $stmt->execute();
            return $stmt->get_result();
      }

      public function searchBetweenDates($dateDebut, $dateFin)
      {
            $dateDebut = $this->normalizeDateTime($dateDebut);
            $dateFin = $this->normalizeDateTime($dateFin);
            $stmt = $this->conn->prepare(
                  "SELECT * FROM {$this->table}
                   WHERE date_envoi BETWEEN ? AND ?
                   ORDER BY date_envoi DESC"
            );
            $stmt->bind_param("ss", $dateDebut, $dateFin);
            $stmt->execute();
            return $stmt->get_result();
      }
}
