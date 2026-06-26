<?php
// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$basedonnees = "gestion_colis";

// Créer la connexion
$conn = new mysqli($serveur, $utilisateur, $motdepasse, $basedonnees);

// Vérifier la connexion
if ($conn->connect_error) {
      die("Erreur de connexion: " . $conn->connect_error);
}

// Définir le charset à UTF-8
$conn->set_charset("utf8mb4");
