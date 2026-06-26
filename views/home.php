<?php
$itineraire = new Itineraire($conn);
$voiture = new Voiture($conn);
$envoyer = new Envoyer($conn);
$recevoir = new Recevoir($conn);

// Compter les enregistrements
$countItineraire = $itineraire->getAll()->num_rows;
$countVoiture = $voiture->getAll()->num_rows;
$countEnvoyer = $envoyer->getAll()->num_rows;
$countRecevoir = $recevoir->getAll()->num_rows;

// Calculer la recette totale
$resultFrais = $conn->query("SELECT SUM(frais) as total FROM envoyer");
$rowFrais = $resultFrais->fetch_assoc();
$totalFrais = $rowFrais['total'] ?? 0;
?>

<div class="row">
      <div class="col-12">
            <h2 class="mb-5"><i class="fa-solid fa-chart-line me-2"></i>Tableau de Bord - Gestion des Colis</h2>
      </div>
</div>

<!-- STATISTIQUES -->
<div class="row mb-5">
      <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-left-primary">
                  <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                              <div>
                                    <h6 class="text-muted text-uppercase mb-1">Itinéraires</h6>
                                    <h3 class="text-primary"><?php echo $countItineraire; ?></h3>
                              </div>
                              <div class="stat-icon text-primary"><i class="fa-solid fa-route"></i></div>
                        </div>
                  </div>
            </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-left-success">
                  <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                              <div>
                                    <h6 class="text-muted text-uppercase mb-1">Voitures</h6>
                                    <h3 class="text-success"><?php echo $countVoiture; ?></h3>
                              </div>
                              <div class="stat-icon text-success"><i class="fa-solid fa-car-side"></i></div>
                        </div>
                  </div>
            </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-left-info">
                  <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                              <div>
                                    <h6 class="text-muted text-uppercase mb-1">Envois</h6>
                                    <h3 class="text-info"><?php echo $countEnvoyer; ?></h3>
                              </div>
                              <div class="stat-icon text-info"><i class="fa-solid fa-box"></i></div>
                        </div>
                  </div>
            </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-left-warning">
                  <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                              <div>
                                    <h6 class="text-muted text-uppercase mb-1">Réceptions</h6>
                                    <h3 class="text-warning"><?php echo $countRecevoir; ?></h3>
                              </div>
                              <div class="stat-icon text-warning"><i class="fa-solid fa-clipboard-check"></i></div>
                        </div>
                  </div>
            </div>
      </div>
</div>

<!-- RECETTE TOTALE -->
<div class="row mb-5">
      <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                  <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-sack-dollar me-2"></i>Recette Totale</h5>
                        <h2 style="font-size: 2.5rem; font-weight: 700;"><?php echo number_format($totalFrais, 0, ',', ' '); ?> Ar</h2>
                        <p class="text-white-50">Total des frais d'envoi enregistrés</p>
                  </div>
            </div>
      </div>
</div>

<!-- ACCÈS RAPIDE AUX SECTIONS -->
<div class="row">
      <div class="col-12">
            <h4 class="mb-4"><i class="fa-solid fa-bolt me-2"></i>Accès Rapide</h4>
      </div>

      <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center h-100 shadow-sm">
                  <div class="card-body">
                        <div class="quick-card-icon"><i class="fa-solid fa-route"></i></div>
                        <h5 class="card-title">Itinéraires</h5>
                        <p class="text-muted"><?php echo $countItineraire; ?> itinéraire(s) enregistré(s)</p>
                        <a href="?page=itineraire" class="btn btn-primary btn-sm">Gérer</a>
                  </div>
            </div>
      </div>

      <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center h-100 shadow-sm">
                  <div class="card-body">
                        <div class="quick-card-icon"><i class="fa-solid fa-car-side"></i></div>
                        <h5 class="card-title">Voitures</h5>
                        <p class="text-muted"><?php echo $countVoiture; ?> voiture(s) enregistrée(s)</p>
                        <a href="?page=voiture" class="btn btn-success btn-sm">Gérer</a>
                  </div>
            </div>
      </div>

      <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center h-100 shadow-sm">
                  <div class="card-body">
                        <div class="quick-card-icon"><i class="fa-solid fa-box"></i></div>
                        <h5 class="card-title">Envois</h5>
                        <p class="text-muted"><?php echo $countEnvoyer; ?> envoi(s) enregistré(s)</p>
                        <a href="?page=envoyer" class="btn btn-info btn-sm">Gérer</a>
                  </div>
            </div>
      </div>

      <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center h-100 shadow-sm">
                  <div class="card-body">
                        <div class="quick-card-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                        <h5 class="card-title">Réceptions</h5>
                        <p class="text-muted"><?php echo $countRecevoir; ?> réception(s) enregistrée(s)</p>
                        <a href="?page=recevoir" class="btn btn-warning btn-sm">Gérer</a>
                  </div>
            </div>
      </div>
</div>

<!-- GUIDE D'UTILISATION -->
<div class="row mt-5">
      <div class="col-12">
            <div class="card bg-light">
                  <div class="card-body">
                        <h5><i class="fa-solid fa-book-open me-2"></i>Guide d'Utilisation</h5>
                        <ol>
                              <li>Commencez par créer des <strong>Itinéraires</strong></li>
                              <li>Ajoutez des <strong>Voitures</strong> pour chaque itinéraire</li>
                              <li>Enregistrez les <strong>Envois</strong> de colis</li>
                              <li>Finalisez avec les <strong>Réceptions</strong> de colis</li>
                        </ol>
                  </div>
            </div>
      </div>
</div>
