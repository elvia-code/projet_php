<?php
$itineraire = new Itineraire($conn);
$edit_mode = false;
$edit_data = null;
$message = '';
$message_type = '';

// CREATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
      if ($itineraire->create($_POST['codeit'], $_POST['villedep'], $_POST['villearr'])) {
            $message = 'Itinéraire ajouté avec succès!';
            $message_type = 'success';
      } else {
            $message = $itineraire->getLastError() ?: 'Erreur lors de l\'ajout!';
            $message_type = 'danger';
      }
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
      if ($itineraire->update($_POST['codeit'], $_POST['villedep'], $_POST['villearr'])) {
            $message = 'Itinéraire modifié avec succès!';
            $message_type = 'success';
      } else {
            $message = $itineraire->getLastError() ?: 'Erreur lors de la modification!';
            $message_type = 'danger';
      }
}

// DELETE
if (isset($_GET['delete'])) {
      if ($itineraire->delete($_GET['delete'])) {
            $message = 'Itinéraire supprimé avec succès!';
            $message_type = 'success';
      } else {
            $message = $itineraire->getLastError() ?: 'Erreur lors de la suppression!';
            $message_type = 'danger';
      }
}

// EDIT
if (isset($_GET['edit'])) {
      $edit_mode = true;
      $result = $itineraire->getById($_GET['edit']);
      $edit_data = $result->fetch_assoc();
}
                                          // AFFICHER LES MESSAGES DE SUCCÈS
                                          if (isset($_GET['success'])) {
                                                if ($_GET['success'] == 'create') {
                                                      echo '<div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-1"></i> Itinéraire ajouté avec succès! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                                }
                                                if ($_GET['success'] == 'update') {
                                                      echo '<div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-1"></i> Itinéraire modifié avec succès! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                                }
                                                if ($_GET['success'] == 'delete') {
                                                      echo '<div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-1"></i> Itinéraire supprimé avec succès! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                                }
                                          }

                                          ?>

<?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <i class="fa-solid fa-circle-<?php echo $message_type === 'success' ? 'check' : 'exclamation'; ?> me-1"></i> <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
<?php endif; ?>
<h2>Gestion des Itinéraires</h2>

<div class="row mb-4">
      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-primary text-white">
                        <?php echo $edit_mode ? 'Modifier un Itinéraire' : 'Ajouter un Itinéraire'; ?>
                  </div>
                  <div class="card-body">
                        <form method="POST">
                              <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">

                              <div class="mb-3">
                                    <label>Code Itinéraire</label>
                                    <input type="text" name="codeit" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['codeit']) : ''; ?>"
                                          <?php echo $edit_mode ? 'readonly' : ''; ?> required>
                              </div>

                              <div class="mb-3">
                                    <label>Ville de Départ</label>
                                    <input type="text" name="villedep" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['villedep']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Ville d'Arrivée</label>
                                    <input type="text" name="villearr" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['villearr']) : ''; ?>" required>
                              </div>

                              <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_mode ? 'Modifier' : 'Ajouter'; ?>
                              </button>

                              <?php if ($edit_mode): ?>
                                    <a href="?page=itineraire" class="btn btn-secondary">Annuler</a>
                              <?php endif; ?>
                        </form>
                  </div>
            </div>
      </div>

      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-info text-white">Liste des Itinéraires</div>
                  <div class="card-body">
                        <div class="table-responsive">
                              <table class="table table-striped">
                                    <thead>
                                          <tr>
                                                <th>Code</th>
                                                <th>De</th>
                                                <th>Vers</th>
                                                <th>Actions</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                          <?php
                                          $result = $itineraire->getAll();
                                          if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                      echo '<tr>';
                                                      echo '<td>' . htmlspecialchars($row['codeit']) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['villedep']) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['villearr']) . '</td>';
                                                      echo '<td>';
                                                      echo '<a href="?page=itineraire&edit=' . htmlspecialchars($row['codeit']) . '" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square me-1"></i></a> ';
                                                      echo '<a href="?page=itineraire&delete=' . htmlspecialchars($row['codeit']) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Confirmer la suppression?\')"><i class="fa-solid fa-trash-can me-1"></i></a>';
                                                      echo '</td>';
                                                      echo '</tr>';
                                                }
                                          } else {
                                                echo '<tr><td colspan="4" class="text-center text-muted">Aucun itinéraire enregistré</td></tr>';
                                          }
                                          ?>
                                    </tbody>
                              </table>
                        </div>
                  </div>
            </div>
      </div>
</div>

<!-- Navigation -->
<div class="row mt-5">
      <div class="col-12">
            <div class="card bg-light">
                  <div class="card-body">
                        <h5>Étape 1/4 : Itinéraires <i class="fa-solid fa-circle-check text-success"></i></h5>
                        <p class="text-muted">Vous avez configuré les itinéraires. Passez à l'étape suivante.</p>
                        <a href="?page=voiture" class="btn btn-primary">Suivant: Voitures →</a>
                  </div>
            </div>
      </div>
</div>
