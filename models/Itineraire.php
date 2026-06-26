<?php
require_once __DIR__ . '/../config/database.php';

class Itineraire
{
      private $conn;
      private $table = 'itineraire';
      private $lastError = '';

      public function __construct($conn)
      {
            $this->conn = $conn;
      }

      public function create($codeit, $villedep, $villearr)
      {
            try {
                  $stmt = $this->conn->prepare("INSERT INTO {$this->table} (codeit, villedep, villearr) VALUES (?, ?, ?)");
                  $stmt->bind_param("sss", $codeit, $villedep, $villearr);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible d'ajouter cet itinéraire. Vérifiez que le code n'existe pas déjà.";
                  return false;
            }
      }

      public function getAll()
      {
            return $this->conn->query("SELECT * FROM {$this->table} ORDER BY codeit");
      }

      public function getById($codeit)
      {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE codeit = ?");
            $stmt->bind_param("s", $codeit);
            $stmt->execute();
            return $stmt->get_result();
      }

      public function update($codeit, $villedep, $villearr)
      {
            try {
                  $stmt = $this->conn->prepare("UPDATE {$this->table} SET villedep = ?, villearr = ? WHERE codeit = ?");
                  $stmt->bind_param("sss", $villedep, $villearr, $codeit);
                  return $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                  $this->lastError = "Impossible de modifier cet itinéraire.";
                  return false;
            }
      }

      public function delete($codeit)
      {
            try {
                  $envoyerIdColumn = $this->envoyerIdColumn();
                  $recevoirEnvoiColumn = $this->recevoirEnvoiColumn();

                  $this->conn->begin_transaction();

                  $stmt = $this->conn->prepare(
                        "DELETE r
                         FROM recevoir r
                         INNER JOIN envoyer e ON r.{$recevoirEnvoiColumn} = e.{$envoyerIdColumn}
                         INNER JOIN voiture v ON e.idvoit = v.idvoit
                         WHERE v.codeit = ?"
                  );
                  $stmt->bind_param("s", $codeit);
                  $stmt->execute();

                  $stmt = $this->conn->prepare(
                        "DELETE e
                         FROM envoyer e
                         INNER JOIN voiture v ON e.idvoit = v.idvoit
                         WHERE v.codeit = ?"
                  );
                  $stmt->bind_param("s", $codeit);
                  $stmt->execute();

                  $stmt = $this->conn->prepare("DELETE FROM voiture WHERE codeit = ?");
                  $stmt->bind_param("s", $codeit);
                  $stmt->execute();

                  $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE codeit = ?");
                  $stmt->bind_param("s", $codeit);
                  $deleted = $stmt->execute();

                  $this->conn->commit();
                  return $deleted;
            } catch (mysqli_sql_exception $e) {
                  $this->conn->rollback();
                  $this->lastError = "Impossible de supprimer cet itinéraire et ses données liées.";
                  return false;
            }
      }

      private function envoyerIdColumn()
      {
            return $this->columnExists('envoyer', 'idenvoi') ? 'idenvoi' : 'denvoi';
      }

      private function recevoirEnvoiColumn()
      {
            return $this->columnExists('recevoir', 'idenvoi') ? 'idenvoi' : 'denvoi';
      }

      private function columnExists($table, $column)
      {
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
            return (int) $row['total'] > 0;
      }

      public function getLastError()
      {
            return $this->lastError;
      }
}
