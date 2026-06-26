<?php
$voiture = new Voiture($conn);
$edit_mode = false;
$edit_data = null;
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
      if ($voiture->create($_POST['idvoit'], $_POST['design'], $_POST['codeit'], $_POST['frais'])) {
            $message = 'Voiture ajoutée avec succès!';
            $message_type = 'success';

            echo '<script>
            setTimeout(function(){
                window.location.href = "?page=voiture";
            }, 2000);
        </script>';
      } else {
            $message = $voiture->getLastError() ?: 'Erreur lors de l\'ajout de la voiture.';
            $message_type = 'danger';
      }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
      if ($voiture->update($_POST['idvoit'], $_POST['design'], $_POST['codeit'], $_POST['frais'])) {
            $message = 'Voiture modifiée avec succès!';
            $message_type = 'success';

            echo '<script>
            setTimeout(function(){
                window.location.href = "?page=voiture";
            }, 2000);
        </script>';
      } else {
            $message = $voiture->getLastError() ?: 'Erreur lors de la modification de la voiture.';
            $message_type = 'danger';
      }
}

if (isset($_GET['delete'])) {
      if ($voiture->delete($_GET['delete'])) {
            $message = 'Voiture supprimée avec succès!';
            $message_type = 'success';
      } else {
            $message = $voiture->getLastError() ?: 'Erreur lors de la suppression de la voiture.';
            $message_type = 'danger';
      }
}

if (isset($_GET['edit'])) {
      $edit_mode = true;
      $result = $voiture->getById($_GET['edit']);
      $edit_data = $result->fetch_assoc();
}
?>

<?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <i class="fa-solid fa-circle-<?php echo $message_type === 'success' ? 'check' : 'exclamation'; ?> me-1"></i> <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
<?php endif; ?>

<h2>Gestion des Voitures</h2>

<div class="row mb-4">
      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-primary text-white">
                        <?php echo $edit_mode ? 'Modifier une Voiture' : 'Ajouter une Voiture'; ?>
                  </div>
                  <div class="card-body">
                        <form method="POST">
                              <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">

                              <div class="mb-3">
                                    <label class="form-label">ID Voiture</label>
                                    <input type="text" name="idvoit" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['idvoit']) : ''; ?>"
                                          <?php echo $edit_mode ? 'readonly' : ''; ?> required>
                              </div>

                              <div class="mb-3">
                                    <label class="form-label">Design/Modèle</label>
                                    <input type="text" name="design" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['design']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label class="form-label">Code Itinéraire</label>
                                    <input type="text" name="codeit" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['codeit']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label class="form-label">Frais (Ar)</label>
                                    <input type="number" name="frais" class="form-control"
                                          value="<?php echo $edit_mode ? $edit_data['frais'] : ''; ?>" required>
                              </div>

                              <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_mode ? 'Modifier' : 'Ajouter'; ?>
                              </button>

                              <?php if ($edit_mode): ?>
                                    <a href="?page=voiture" class="btn btn-secondary">Annuler</a>
                              <?php endif; ?>
                        </form>
                  </div>
            </div>
      </div>

      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-info text-white">Liste des Voitures</div>
                  <div class="card-body">
                        <div class="table-responsive">
                              <table class="table table-striped table-sm">
                                    <thead>
                                          <tr>
                                                <th>ID</th>
                                                <th>Design</th>
                                                <th>Itinéraire</th>
                                                <th>Frais</th>
                                                <th>Actions</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                          <?php
                                          $result = $voiture->getAll();
                                          if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                      echo '<tr>';
                                                      echo '<td>' . htmlspecialchars($row['idvoit']) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['design']) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['codeit']) . '</td>';
                                                      echo '<td>' . $row['frais'] . ' Ar</td>';
                                                      echo '<td>';
                                                      echo '<a href="?page=voiture&edit=' . htmlspecialchars($row['idvoit']) . '" class="btn btn-warning btn-sm" title="Modifier"><i class="fa-solid fa-pen-to-square"></i></a> ';
                                                      echo '<a href="?page=voiture&delete=' . htmlspecialchars($row['idvoit']) . '" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm(\'Confirmer?\')"><i class="fa-solid fa-trash-can"></i></a>';
                                                      echo '</td>';
                                                      echo '</tr>';
                                                }
                                          } else {
                                                echo '<tr><td colspan="5" class="text-center text-muted">Aucune voiture enregistrée</td></tr>';
                                          }
                                          ?>
                                    </tbody>
                              </table>
                        </div>
                  </div>
            </div>
      </div>
</div>

<div class="row mt-5">
      <div class="col-12">
            <div class="card bg-light">
                  <div class="card-body">
                        <h5>Étape 2/4 : Voitures <i class="fa-solid fa-circle-check text-success"></i></h5>
                        <p class="text-muted">Vous avez configuré les voitures. Passez à l'étape suivante.</p>
                        <a href="?page=itineraire" class="btn btn-secondary">← Retour: Itinéraires</a>
                        <a href="?page=envoyer" class="btn btn-primary">Suivant: Envois →</a>
                  </div>
            </div>
      </div>
</div>
