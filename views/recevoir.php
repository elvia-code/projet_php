<?php
$recevoir = new Recevoir($conn);
$idColumn = $recevoir->getIdColumn();
$envoiColumn = $recevoir->getEnvoiColumn();
$edit_mode = false;
$edit_data = null;
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
      if ($recevoir->create($_POST['idenvoi'], $_POST['date_recept'])) {
            $message = 'Réception enregistrée avec succès!';
            $message_type = 'success';
            $mailStatus = $recevoir->getLastMailStatus();
            if ($mailStatus && !$mailStatus['success']) {
                  $message .= ' ' . $mailStatus['message'];
                  $message_type = 'warning';
            } elseif ($mailStatus && $mailStatus['success']) {
                  $message .= ' ' . $mailStatus['message'];
            }
      } else {
            $message = $recevoir->getLastError() ?: 'Erreur lors de l\'enregistrement de la réception.';
            $message_type = 'danger';
      }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
      if ($recevoir->update($_POST['idrecept'], $_POST['idenvoi'], $_POST['date_recept'])) {
            $message = 'Réception modifiée avec succès!';
            $message_type = 'success';
      } else {
            $message = $recevoir->getLastError() ?: 'Erreur lors de la modification de la réception.';
            $message_type = 'danger';
      }
}

if (isset($_GET['delete'])) {
      if ($recevoir->delete($_GET['delete'])) {
            $message = 'Réception supprimée avec succès!';
            $message_type = 'success';
      } else {
            $message = $recevoir->getLastError() ?: 'Erreur lors de la suppression de la réception.';
            $message_type = 'danger';
      }
}

if (isset($_GET['mail'])) {
      $mailStatus = $recevoir->resendMail($_GET['mail']);
      $message = $mailStatus['message'];
      $message_type = $mailStatus['success'] ? 'success' : 'warning';
}

if (isset($_GET['edit'])) {
      $edit_mode = true;
      $result = $recevoir->getById($_GET['edit']);
      $edit_data = $result->fetch_assoc();
}
?>

<?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <i class="fa-solid fa-circle-<?php echo $message_type === 'success' ? 'check' : 'exclamation'; ?> me-1"></i> <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
<?php endif; ?>

<h2>Gestion des Réceptions</h2>

<div class="row mb-4">
      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-primary text-white">
                        <?php echo $edit_mode ? 'Modifier une Réception' : 'Enregistrer une Réception'; ?>
                  </div>
                  <div class="card-body">
                        <form method="POST">
                              <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">

                              <?php if ($edit_mode): ?>
                                    <input type="hidden" name="idrecept" value="<?php echo htmlspecialchars($edit_data[$idColumn]); ?>">
                              <?php endif; ?>

                              <div class="mb-3">
                                    <label>ID Envoi</label>
                                    <input type="number" name="idenvoi" class="form-control"
                                          value="<?php echo $edit_mode ? htmlspecialchars($edit_data[$envoiColumn]) : ''; ?>" required>
                              </div>

                              <div class="mb-3">
                                    <label>Date Réception</label>
                                    <input type="datetime-local" name="date_recept" class="form-control"
                                          value="<?php echo $edit_mode ? str_replace(' ', 'T', $edit_data['date_recept']) : ''; ?>" required>
                              </div>

                              <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_mode ? 'Modifier' : 'Enregistrer'; ?>
                              </button>

                              <?php if ($edit_mode): ?>
                                    <a href="?page=recevoir" class="btn btn-secondary">Annuler</a>
                              <?php endif; ?>
                        </form>
                  </div>
            </div>
      </div>

      <div class="col-md-6">
            <div class="card">
                  <div class="card-header bg-info text-white">Liste des Réceptions</div>
                  <div class="card-body">
                        <div class="table-responsive">
                              <table class="table table-striped">
                                    <thead>
                                          <tr>
                                                <th>ID Réception</th>
                                                <th>ID Envoi</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                          <?php
                                          $result = $recevoir->getAll();
                                          if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                      echo '<tr>';
                                                      echo '<td>' . htmlspecialchars($row[$idColumn]) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row[$envoiColumn]) . '</td>';
                                                      echo '<td>' . htmlspecialchars($row['date_recept']) . '</td>';
                                                      echo '<td>';
                                                      echo '<a href="?page=recevoir&edit=' . htmlspecialchars($row[$idColumn]) . '" class="btn btn-warning btn-sm" title="Modifier"><i class="fa-solid fa-pen-to-square"></i></a> ';
                                                      echo '<a href="?page=recevoir&mail=' . htmlspecialchars($row[$idColumn]) . '" class="btn btn-primary btn-sm" title="Envoyer mail"><i class="fa-solid fa-envelope"></i></a> ';
                                                      echo '<a href="?page=recevoir&delete=' . htmlspecialchars($row[$idColumn]) . '" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm(\'Confirmer?\')"><i class="fa-solid fa-trash-can"></i></a>';
                                                      echo '</td>';
                                                      echo '</tr>';
                                                }
                                          } else {
                                                echo '<tr><td colspan="4" class="text-center text-muted">Aucune réception</td></tr>';
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
                        <h5>Réceptions <i class="fa-solid fa-circle-check text-success"></i></h5>
                        <p class="text-muted">Vous avez enregistré toutes les réceptions.</p>
                        <a href="?page=envoyer" class="btn btn-secondary">← Retour: Envois</a>
                        <a href="?page=home" class="btn btn-success"><i class="fa-solid fa-house me-1"></i> Retour à l'accueil</a>
                  </div>
            </div>
      </div>
</div>
