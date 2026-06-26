<?php

require('fpdf186/fpdf.php');
require_once 'config/database.php';

if (!isset($_GET['id'])) {
      die("ID manquant");
}

$id = intval($_GET['id']);

function tableColumnExists($conn, $table, $column)
{
      $stmt = $conn->prepare(
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

$idColumn = tableColumnExists($conn, 'envoyer', 'idenvoi') ? 'idenvoi' : 'denvoi';

$sql = "SELECT e.*, v.design, i.villearr
        FROM envoyer e
        LEFT JOIN voiture v ON e.idvoit = v.idvoit
        LEFT JOIN itineraire i ON v.codeit = i.codeit
        WHERE e.{$idColumn} = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
      die("Envoi introuvable");
}

$data = $result->fetch_assoc();
$date = new DateTime($data['date_envoi']);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(18, 18, 18);

$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'RECU DE CLIENT POUR UN ENVOI', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'Recu N ' . ($data[$idColumn] ?? ''), 0, 1);
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Date d'envoi : " . $date->format('d/m/Y H:i:s'), 0, 1);
$pdf->Cell(0, 8, "Nom de l'Envoyeur : " . ($data['nomEnvoyeur'] ?? ''), 0, 1);
$pdf->Cell(0, 8, 'Voiture N ' . ($data['idvoit'] ?? '') . ' / Destination = ' . ($data['villearr'] ?? 'Non definie'), 0, 1);
$pdf->Cell(0, 8, 'Colis = ' . ($data['colis'] ?? ''), 0, 1);
$pdf->Cell(0, 8, 'Frais : ' . number_format($data['frais'] ?? 0, 0, ',', ' ') . ' Ar', 0, 1);
$pdf->Cell(0, 8, 'Nom du Recepteur : ' . ($data['nomRecepteur'] ?? ''), 0, 1);
$pdf->Cell(0, 8, 'Contact du Recepteur : ' . ($data['contactRecepteur'] ?? ''), 0, 1);

$pdf->Ln(14);
$pdf->SetFont('Arial', 'I', 11);
$pdf->Cell(0, 8, "Merci d'avoir utilise notre service.", 0, 1, 'C');

$pdf->Output('I', 'recu-envoi-' . ($data[$idColumn] ?? $id) . '.pdf');
