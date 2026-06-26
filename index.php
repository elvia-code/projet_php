<?php

require_once 'config/database.php';
require_once 'models/Itineraire.php';
require_once 'models/Voiture.php';
require_once 'models/Envoyer.php';
require_once 'models/Recevoir.php';
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Gestion des Colis - Coopérative</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
      <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
            <div class="container">
                  <a class="navbar-brand" href="index.php"><i class="fas fa-box"></i> Gestion Colis</a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                              <li class="nav-item"><a class="nav-link" href="index.php?page=itineraire">Itinéraires</a></li>
                              <li class="nav-item"><a class="nav-link" href="index.php?page=voiture">Voitures</a></li>
                              <li class="nav-item"><a class="nav-link" href="index.php?page=envoyer">Envois</a></li>
                              <li class="nav-item"><a class="nav-link" href="index.php?page=recevoir">Réceptions</a></li>
                              <li class="nav-item"><a class="nav-link" href="?page=recherche"><i class="fa-solid fa-magnifying-glass me-1"></i> Recherche</a></li>
                        </ul>
                  </div>
            </div>
      </nav>

      <main class="container mt-5">
            <?php
            switch ($page) {
                  case 'itineraire':
                        include 'views/itineraire.php';
                        break;
                  case 'voiture':
                        include 'views/voiture.php';
                        break;
                  case 'envoyer':
                        include 'views/envoyer.php';
                        break;
                  case 'recevoir':
                        include 'views/recevoir.php';
                        break;
                  case 'recherche':
                        include 'views/recherche.php';
                        break;
                  default:
                        include 'views/home.php';
            }
            ?>
      </main>

      <footer class="bg-dark text-white text-center py-4 mt-5">
            <p>&copy; 2026 Gestion des Colis - Coopérative</p>
      </footer>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

      <div class="quick-nav">
            <a href="?page=home" class="btn btn-primary" title="Dashboard"><i class="fa-solid fa-house"></i></a>
            <a href="?page=itineraire" class="btn btn-info" title="Itinéraires"><i class="fa-solid fa-route"></i></a>
            <a href="?page=voiture" class="btn btn-success" title="Voitures"><i class="fa-solid fa-car-side"></i></a>
            <a href="?page=envoyer" class="btn btn-warning" title="Envois"><i class="fa-solid fa-box"></i></a>
            <a href="?page=recevoir" class="btn btn-danger" title="Réceptions"><i class="fa-solid fa-clipboard-check"></i></a>
      </div>
</body>

</html>
