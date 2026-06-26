<?php
$envoyer = new Envoyer($conn);
$idColumn = $envoyer->getIdColumn();
$edit_mode = false;
$edit_data = null;
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
      if ($envoyer->create($_POST['idvoit'], $_POST['colis'], $_POST['nomEnvoyeur'], $_POST['emailEnvoyeur'], $_POST['date_envoi'], $_POST['frais'], $_POST['nomRecepteur'], $_POST['contactRecepteur'])) {
            $message = 'Envoi enregistré avec succès!';
            $message_type = 'success';

            // REDIRECTION AUTO APRÈS 2 secondes
            echo '<script>
            setTimeout(function(){
                window.location.href = "?page=envoyer";
            }, 2000);
        </script>';
      } else {
            $message = $envoyer->getLastError() ?: 'Erreur lors de l\'enregistrement de l\'envoi.';
            $message_type = 'danger';
      }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
      if ($envoyer->update($_POST['idenvoi'], $_POST['idvoit'], $_POST['colis'], $_POST['nomEnvoyeur'], $_POST['emailEnvoyeur'], $_POST['date_envoi'], $_POST['frais'], $_POST['nomRecepteur'], $_POST['contactRecepteur'])) {
            $message = 'Envoi modifié avec succès!';
            $message_type = 'success';
      } else {
            $message = $envoyer->getLastError() ?: 'Erreur lors de la modification de l\'envoi.';
            $message_type = 'danger';
      }
}

if (isset($_GET['delete'])) {
      if ($envoyer->delete($_GET['delete'])) {
            $message = 'Envoi supprimé avec succès!';
            $message_type = 'success';
      } else {
            $message = $envoyer->getLastError() ?: 'Erreur lors de la suppression de l\'envoi.';
            $message_type = 'danger';
      }
}

if (isset($_GET['edit'])) {
      $edit_mode = true;
      $result = $envoyer->getById($_GET['edit']);
      $edit_data = $result->fetch_assoc();
}
?>

<?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <i class="fa-solid fa-circle-<?php echo $message_type === 'success' ? 'check' : 'exclamation'; ?> me-1"></i> <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
<?php endif; ?>

<h2>Gestion des Envois</h2>

<div class="row mb-4">
      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-primary text-white">
                        <?php echo $edit_mode ? 'Modifier un Envoi' : 'Ajouter un Envoi'; ?>
                  </div>
                  <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <form method="POST">
                              <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">

                              <?php if ($edit_mode): ?>
                                    <input type="hidden" name="idenvoi" value="<?php echo htmlspecialchars($edit_data[$idColumn]); ?>">
                              <?php endif; ?>

                              <div class="mb-3">
                                    <label>ID Voiture</label>
                                    <input type="text" name="idvoit" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['idvoit']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Description Colis</label>
                                    <input type="text" name="colis" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['colis']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Nom Envoyeur</label>
                                    <input type="text" name="nomEnvoyeur" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nomEnvoyeur']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Email Envoyeur</label>
                                    <input type="email" name="emailEnvoyeur" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['emailEnvoyeur']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Date Envoi</label>
                                    <input type="datetime-local" name="date_envoi" class="form-control"
                                          value="<?php echo $edit_mode ? str_replace(' ', 'T', $edit_data['date_envoi']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Frais (Ar)</label>
                                    <input type="number" name="frais" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['frais']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Nom Récepteur</label>
                                    <input type="text" name="nomRecepteur" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nomRecepteur']) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Contact Récepteur</label>
                                    <input type="text" name="contactRecepteur" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data['contactRecepteur']) : ''; ?>" required>
                              </div>

                              <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_mode ? 'Modifier' : 'Ajouter'; ?>
                              </button>

                              <?php if ($edit_mode): ?>
                                    <a href="?page=envoyer" class="btn btn-secondary">Annuler</a>
                              <?php endif; ?>
                        </form>
                  </div>
            </div>
      </div>

      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-info text-white">Liste des Envois</div>
                  <div class="card-body">
                        <div class="table-responsive">
                              <table class="table table-striped table-sm">
                                    <thead>
                                          <tr>
                                                <th>ID</th>
                                                <th>Colis</th>
                                                <th>Envoyeur</th>
                                                <th>Récepteur</th>
                                                <th>Actions</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                          <?php
                                          $result = $envoyer->getAll();
                                          if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                      echo '<tr>';
                                                      echo '<td>' . htmlspecialchars($row[$idColumn]) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['colis']) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['nomEnvoyeur']) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['nomRecepteur']) . '</td>';
                                                      echo '<td>';
                                                      echo '<a href="?page=envoyer&edit=' . htmlspecialchars($row[$idColumn]) . '" class="btn btn-warning btn-sm" title="Modifier"><i class="fa-solid fa-pen-to-square"></i></a> ';

                                                      echo '<a href="?page=envoyer&delete=' . htmlspecialchars($row[$idColumn]) . '" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm(\'Confirmer?\')"><i class="fa-solid fa-trash-can"></i></a> ';

                                                      echo '<a href="pdf_recu.php?id=' . htmlspecialchars($row[$idColumn]) . '" class="btn btn-primary btn-sm" target="_blank"><i class="fa-solid fa-file-pdf me-1"></i> PDF</a>';

                                                      echo '</td>';
                                                      echo '</tr>';
                                                }
                                          } else {
                                                echo '<tr><td colspan="5" class="text-center text-muted">Aucun envoi</td></tr>';
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
                        <h5>Envois <i class="fa-solid fa-circle-check text-success"></i></h5>
                        <p class="text-muted">Vous avez enregistré les envois. Passez à l'étape suivante.</p>
                        <a href="?page=voiture" class="btn btn-secondary">← Retour: Voitures</a>
                        <a href="?page=recevoir" class="btn btn-primary">Suivant: Réceptions →</a>
                  </div>
            </div>
      </div>
</div>
