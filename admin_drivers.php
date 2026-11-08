<?php
/*
 * Fichier : admin_driver.php
 * Rôle : Gestion des chauffeurs (Ajout, Modification, Suppression)
 */
require_once 'config/db.php';

// Variables pour le mode édition
$edit_mode = false;
$driver_a_modifier = [
    'id' => '',
    'nom' => '',
    'prenom' => '',
    'telephone' => '',
    'statut' => 'disponible'
];

$message = "";
$error = "";

// 1. Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_del = intval($_GET['id']);
    try {
        $stmt_del = $pdo->prepare("DELETE FROM drivers WHERE id = ?");
        $stmt_del->execute([$id_del]);
        $message = "Chauffeur supprimé avec succès !";
    } catch (PDOException $e) {
        $error = "Impossible de supprimer ce chauffeur (il est peut-être lié à une réservation).";
    }
}

// 2. Récupération des données en mode édition
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id_edit = intval($_GET['id']);
    $stmt_edit = $pdo->prepare("SELECT * FROM drivers WHERE id = ?");
    $stmt_edit->execute([$id_edit]);
    $res = $stmt_edit->fetch(PDO::FETCH_ASSOC);
    if ($res) {
        $edit_mode = true;
        $driver_a_modifier = $res;
    }
}

// 3. Traitement du formulaire (Ajout ou Modification)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sauvegarder_driver'])) {
    $snom = trim($_POST['nom'] ?? '');
    $sprenom = trim($_POST['prenom'] ?? '');
    $stelephone = trim($_POST['telephone'] ?? '');
    $sstatut = $_POST['statut'] ?? 'disponible';
    $sid = $_POST['id_driver'] ?? '';

    if (!empty($snom) && !empty($sprenom) && !empty($stelephone)) {
        try {
            if (!empty($sid)) {
                $sql = "UPDATE drivers SET nom = :nom, prenom = :prenom, telephone = :telephone, statut = :statut WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $sid, PDO::PARAM_INT);
            } else {
                $sql = "INSERT INTO drivers (nom, prenom, telephone, statut) VALUES (:nom, :prenom, :telephone, :statut)";
                $stmt = $pdo->prepare($sql);
            }

            $stmt->bindValue(':nom', $snom);
            $stmt->bindValue(':prenom', $sprenom);
            $stmt->bindValue(':telephone', $stelephone);
            $stmt->bindValue(':statut', $sstatut);
            $stmt->execute();

            $message = !empty($sid) ? "Chauffeur mis à jour avec succès !" : "Chauffeur ajouté avec succès !";
            
            $edit_mode = false;
            $driver_a_modifier = ['id' => '', 'nom' => '', 'prenom' => '', 'telephone' => '', 'statut' => 'disponible'];
        } catch (PDOException $e) {
            $error = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Récupération de la liste des chauffeurs
$stmt_drv = $pdo->query("SELECT * FROM drivers ORDER BY id DESC");
$drivers = $stmt_drv->fetchAll(PDO::FETCH_ASSOC);

// Inclusion de l'en-tête admin unifié
include 'includes/admin_header.php';
?>

<link rel="stylesheet" href="assets/css/admin_drivers.css">

<main class="admin-main">
    <h1>Gestion des Chauffeurs</h1>
    <a href="admin_dashboard.php" class="back-link">← Retour au tableau de bord</a>

    <?php if (!empty($message)): ?>
        <div class="alert-success"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout / modification -->
    <div class="form-card">
        <h3><?= $edit_mode ? "Modifier le chauffeur #" . $driver_a_modifier['id'] : "Ajouter un nouveau chauffeur"; ?></h3>
        <form action="" method="POST">

            <input type="hidden" name="id_driver" value="<?= htmlspecialchars($driver_a_modifier['id']); ?>">

            <div class="form-group-admin">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($driver_a_modifier['nom']); ?>" required>
            </div>

            <div class="form-group-admin">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($driver_a_modifier['prenom']); ?>" required>
            </div>

            <div class="form-group-admin">
                <label for="telephone">Téléphone</label>
                <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($driver_a_modifier['telephone']); ?>" required>
            </div>

            <div class="form-group-admin">
                <label for="statut">Statut</label>
                <select name="statut" id="statut">
                    <option value="disponible" <?= ($driver_a_modifier['statut'] === 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                    <option value="en_mission" <?= ($driver_a_modifier['statut'] === 'en_mission') ? 'selected' : ''; ?>>En mission</option>
                </select>
            </div>

            <button type="submit" name="sauvegarder_driver" class="btn-submit">
                <?= $edit_mode ? "Mettre à jour le chauffeur" : "Enregistrer le chauffeur"; ?>
            </button>

            <?php if ($edit_mode): ?>
                <a href="admin_driver.php" style="display: inline-block; margin-top: 15px; text-decoration: none; color: #6b7280; font-size: 14px;">Annuler la modification</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Liste des chauffeurs enregistrés -->
    <h3 class="section-title">Chauffeurs enregistrés dans le système</h3>

    <div class="drivers-grid">
        <?php if (empty($drivers)): ?>
            <p>Aucun chauffeur enregistré pour le moment.</p>
        <?php else: ?>
            <?php foreach ($drivers as $d): ?>
                <div class="driver-card">
                    <div>
                        <h4><?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']); ?></h4>
                        <div class="driver-info">Téléphone : <strong><?= htmlspecialchars($d['telephone']); ?></strong></div>
                        <div class="driver-info">Statut : <em><?= htmlspecialchars($d['statut']); ?></em></div>
                    </div>

                    <!-- Boutons Modifier / Supprimer -->
                    <div class="driver-actions">
                        <a href="admin_driver.php?action=edit&id=<?= $d['id']; ?>" class="btn-edit">Modifier</a>
                        <a href="admin_driver.php?action=delete&id=<?= $d['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce chauffeur ?');">Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>