<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../mail.php';

class Recevoir
{
      private $conn;
      private $table = 'recevoir';
      private $idColumn;
      private $envoiColumn;
      private $lastMailStatus = null;
      private $lastError = '';

      public function __construct($conn)
      {
            $this->conn = $conn;
            $this->idColumn = $this->columnExists('idrecept') ? 'idrecept' : 'drecept';
            $this->envoiColumn = $this->columnExists('idenvoi') ? 'idenvoi' : 'denvoi';
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

      private function envoyerIdColumn()
      {
            $table = 'envoyer';
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

      public function getIdColumn()
      {
            return $this->idColumn;
      }

      public function getEnvoiColumn()
      {
            return $this->envoiColumn;
      }

      public function getLastMailStatus()
      {
            return $this->lastMailStatus;
      }

      private function normalizeDateTime($value)
      {
            $value = str_replace('T', ' ', $value);
            return strlen($value) === 16 ? $value . ':00' : $value;
      }

      public function create($idenvoi, $date_recept)
      {
            try {
                  $date_recept = $this->normalizeDateTime($date_recept);
                  $stmt = $this->conn->prepare("INSERT INTO {$this->table} ({$this->envoiColumn}, date_recept) VALUES (?, ?)");
                  $stmt->bind_param("is", $idenvoi, $date_recept);
                  $created = $stmt->execute();

                  if ($created) {
                        $this->lastMailStatus = $this->sendReceptionMail($idenvoi, $date_recept);
                  }

                  return $created;
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible d'enregistrer cette réception. Vérifiez que l'ID envoi existe.";
                  return false;
            }
      }

      public function getAll()
      {
            return $this->conn->query("SELECT * FROM {$this->table} ORDER BY {$this->idColumn} DESC");
      }

      public function getById($idrecept)
      {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE {$this->idColumn} = ?");
            $stmt->bind_param("i", $idrecept);
            $stmt->execute();
            return $stmt->get_result();
      }

      public function update($idrecept, $idenvoi, $date_recept)
      {
            try {
                  $date_recept = $this->normalizeDateTime($date_recept);
                  $stmt = $this->conn->prepare("UPDATE {$this->table} SET {$this->envoiColumn} = ?, date_recept = ? WHERE {$this->idColumn} = ?");
                  $stmt->bind_param("isi", $idenvoi, $date_recept, $idrecept);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible de modifier cette réception. Vérifiez que l'ID envoi existe.";
                  return false;
            }
      }

      public function delete($idrecept)
      {
            try {
                  $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE {$this->idColumn} = ?");
                  $stmt->bind_param("i", $idrecept);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible de supprimer cette réception.";
                  return false;
            }
      }

      public function getLastError()
      {
            return $this->lastError;
      }

      public function resendMail($idrecept)
      {
            $stmt = $this->conn->prepare("SELECT {$this->envoiColumn}, date_recept FROM {$this->table} WHERE {$this->idColumn} = ?");
            $stmt->bind_param("i", $idrecept);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                  return ['success' => false, 'message' => "Réception introuvable."];
            }

            $reception = $result->fetch_assoc();
            return $this->sendReceptionMail($reception[$this->envoiColumn], $reception['date_recept']);
      }

      private function sendReceptionMail($idenvoi, $date_recept)
      {
            $envoyerIdColumn = $this->envoyerIdColumn();
            $stmt = $this->conn->prepare("SELECT emailEnvoyeur, nomEnvoyeur, colis FROM envoyer WHERE {$envoyerIdColumn} = ?");
            $stmt->bind_param("i", $idenvoi);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                  return ['success' => false, 'message' => "Aucun envoi trouvé pour l'identifiant {$idenvoi}."];
            }

            $envoi = $result->fetch_assoc();
            return envoyerMail($envoi['emailEnvoyeur'], $envoi['nomEnvoyeur'], $envoi['colis'], $date_recept);
      }
}
