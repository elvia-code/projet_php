<?php
$envoyer = new Envoyer($conn);
$idColumn = $envoyer->getIdColumn();
$results = null;
$search_keyword = '';
$date_debut = '';
$date_fin = '';
$search_mode = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
      $search_keyword = $_POST['search'];
      $results = $envoyer->search($search_keyword);
      $search_mode = 'keyword';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date_debut'], $_POST['date_fin'])) {
      $date_debut = $_POST['date_debut'];
      $date_fin = $_POST['date_fin'];
      $results = $envoyer->searchBetweenDates($date_debut, $date_fin);
      $search_mode = 'dates';
}
?>

<h2><i class="fa-solid fa-magnifying-glass me-2"></i>Recherche de Colis</h2>

<div class="row mb-4">
      <div class="col-md-6 mb-4">
            <div class="card">
                  <div class="card-header bg-primary text-white">
                        Recherche par code ou désignation
                  </div>
                  <div class="card-body">
                        <form method="POST" class="row g-3">
                              <div class="col-12">
                                    <label class="form-label">Code d'envoi, colis, envoyeur ou récepteur</label>
                                    <input type="text" name="search" class="form-control"
                                          placeholder="Ex: 1, Documents, Jean..."
                                          value="<?php echo htmlspecialchars($search_keyword); ?>" required>
                              </div>
                              <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Rechercher</button>
                              </div>
                        </form>
                  </div>
            </div>
      </div>

      <div class="col-md-6 mb-4">
            <div class="card">
                  <div class="card-header bg-info text-white">
                        Recherche entre deux dates
                  </div>
                  <div class="card-body">
                        <form method="POST" class="row g-3">
                              <div class="col-md-6">
                                    <label class="form-label">Date début</label>
                                    <input type="datetime-local" name="date_debut" class="form-control"
                                          value="<?php echo htmlspecialchars($date_debut); ?>" required>
                              </div>
                              <div class="col-md-6">
                                    <label class="form-label">Date fin</label>
                                    <input type="datetime-local" name="date_fin" class="form-control"
                                          value="<?php echo htmlspecialchars($date_fin); ?>" required>
                              </div>
                              <div class="col-12">
                                    <button type="submit" class="btn btn-info text-white w-100"><i class="fa-solid fa-calendar-days me-1"></i> Filtrer</button>
                              </div>
                        </form>
                  </div>
            </div>
      </div>
</div>

<?php if ($results !== null): ?>
      <div class="row">
            <div class="col-12">
                  <div class="card">
                        <div class="card-header bg-info text-white">
                              Résultats (<?php echo $results->num_rows; ?> trouvé(s))
                        </div>
                        <div class="card-body">
                              <?php if ($results->num_rows > 0): ?>
                                    <div class="table-responsive">
                                          <table class="table table-striped">
                                                <thead>
                                                      <tr>
                                                            <th>ID</th>
                                                            <th>Colis</th>
                                                            <th>Envoyeur</th>
                                                            <th>Récepteur</th>
                                                            <th>Frais</th>
                                                            <th>Date</th>
                                                      </tr>
                                                </thead>
                                                <tbody>
                                                      <?php while ($row = $results->fetch_assoc()): ?>
                                                            <tr>
                                                                  <td><?php echo htmlspecialchars($row[$idColumn]); ?></td>
                                                                  <td><?php echo htmlspecialchars($row['colis']); ?></td>
                                                                  <td><?php echo htmlspecialchars($row['nomEnvoyeur']); ?></td>
                                                                  <td><?php echo htmlspecialchars($row['nomRecepteur']); ?></td>
                                                                  <td><?php echo number_format((int) $row['frais'], 0, ',', ' '); ?> Ar</td>
                                                                  <td><?php echo htmlspecialchars($row['date_envoi']); ?></td>
                                                            </tr>
                                                      <?php endwhile; ?>
                                                </tbody>
                                          </table>
                                    </div>
                              <?php else: ?>
                                    <div class="alert alert-warning">
                                          <i class="fa-solid fa-circle-xmark me-1"></i> Aucun résultat<?php echo $search_mode === 'keyword' ? ' pour "' . htmlspecialchars($search_keyword) . '"' : ' pour cette période'; ?>
                                    </div>
                              <?php endif; ?>
                        </div>
                  </div>
            </div>
      </div>
<?php endif; ?>

<div class="row mt-5">
      <div class="col-12">
            <a href="?page=home" class="btn btn-success"><i class="fa-solid fa-house me-1"></i> Accueil</a>
      </div>
</div>
