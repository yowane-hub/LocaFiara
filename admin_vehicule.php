<?php
// Connexion centralisée
require_once 'config/db.php';

$message = "";
$error = "";

// Variables pour le mode édition
$edit_mode = false;
$vehicule_a_modifier = [
    'id' => '',
    'marque' => '',
    'modele' => '',
    'immatriculation' => '',
    'prix_par_jour' => '',
    'statut' => 'disponible',
    'category_id' => 1
];

// 1. Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_del = intval($_GET['id']);
    try {
        $stmt_del = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
        $stmt_del->execute([$id_del]);
        $message = "Véhicule supprimé avec succès !";
    } catch (PDOException $e) {
        $error = "Impossible de supprimer ce véhicule (il est peut-être lié à une réservation).";
    }
}

// 2. Récupération des données si on clique sur "Modifier"
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id_edit = intval($_GET['id']);
    $stmt_edit = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt_edit->execute([$id_edit]);
    $res = $stmt_edit->fetch(PDO::FETCH_ASSOC);
    if ($res) {
        $edit_mode = true;
        $vehicule_a_modifier = $res;
    }
}

// 3. Traitement de l'enregistrement (Ajout ou Modification)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sauvegarder_vehicule'])) {
    $id = $_POST['id_vehicule'] ?? '';
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $immatriculation = trim($_POST['immatriculation'] ?? '');
    $prix_par_jour = trim($_POST['prix_par_jour'] ?? '');
    $statut = $_POST['statut'] ?? 'disponible';
    $category_id = $_POST['category_id'] ?? 1;

    if (!empty($marque) && !empty($modele) && !empty($immatriculation) && !empty($prix_par_jour)) {
        
        // Gestion de l'image binaire
        $image_data = null;
        $update_image_sql = "";
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
            $image_data = file_get_contents($_FILES['Image']['tmp_name']);
            $update_image_sql = ", image = :image";
        }

        try {
            if (!empty($id)) {
                // MISE A JOUR (UPDATE)
                $sql = "UPDATE vehicles SET category_id = :category_id, immatriculation = :immatriculation, 
                        marque = :marque, modele = :modele, prix_par_jour = :prix_par_jour, statut = :statut $update_image_sql 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            } else {
                // INSERTION (CREATE)
                $sql = "INSERT INTO vehicles (category_id, immatriculation, marque, modele, prix_par_jour, statut, image) 
                        VALUES (:category_id, :immatriculation, :marque, :modele, :prix_par_jour, :statut, :image)";
                $stmt = $pdo->prepare($sql);
            }

            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':immatriculation', $immatriculation);
            $stmt->bindParam(':marque', $marque);
            $stmt->bindParam(':modele', $modele);
            $stmt->bindParam(':prix_par_jour', $prix_par_jour);
            $stmt->bindParam(':statut', $statut);
            
            // Si on fait un update sans nouvelle image, on ne lie pas le paramètre image s'il n'est pas dans la requête
            if (!empty($image_data) || empty($id)) {
                $stmt->bindParam(':image', $image_data, PDO::PARAM_LOB);
            }

            $stmt->execute();
            $message = !empty($id) ? "Véhicule mis à jour avec succès !" : "Véhicule ajouté avec succès !";
            
            // Réinitialisation du mode édition après succès
            $edit_mode = false;
            $vehicule_a_modifier = ['id' => '', 'marque' => '', 'modele' => '', 'immatriculation' => '', 'prix_par_jour' => '', 'statut' => 'disponible', 'category_id' => 1];

        } catch (PDOException $e) {
            $error = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Récupération de la liste des véhicules
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC");
$vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inclusion du header
include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/admin_vehicule.css">

<main class="admin-main">
    <h1>Gestion des Véhicules</h1>
    <a href="admin_dashboard.php" class="back-link">← Retour au tableau de bord</a>

    <?php if (!empty($message)): ?>
        <div class="alert-success"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout ou de modification -->
    <div class="form-card">
        <h3><?= $edit_mode ? "Modifier le véhicule #" . $vehicule_a_modifier['id'] : "Ajouter un nouveau véhicule"; ?></h3>
        <form action="" method="POST" enctype="multipart/form-data">
            
            <!-- Champ caché pour stocker l'ID en cas de modification -->
            <input type="hidden" name="id_vehicule" value="<?= htmlspecialchars($vehicule_a_modifier['id']); ?>">

            <div class="form-group-admin">
                <label for="marque">Marque</label>
                <input type="text" name="marque" id="marque" value="<?= htmlspecialchars($vehicule_a_modifier['marque']); ?>" required>
            </div>

            <div class="form-group-admin">
                <label for="modele">Modèle</label>
                <input type="text" name="modele" id="modele" value="<?= htmlspecialchars($vehicule_a_modifier['modele']); ?>" required>
            </div>

            <div class="form-group-admin">
                <label for="immatriculation">Immatriculation</label>
                <input type="text" name="immatriculation" id="immatriculation" value="<?= htmlspecialchars($vehicule_a_modifier['immatriculation']); ?>" required>
            </div>

            <div class="form-group-admin">
                <label for="prix_par_jour">Prix par jour (Ar)</label>
                <input type="number" name="prix_par_jour" id="prix_par_jour" value="<?= htmlspecialchars($vehicule_a_modifier['prix_par_jour']); ?>" required>
            </div>

            <div class="form-group-admin">
                <label for="statut">Statut</label>
                <select name="statut" id="statut">
                    <option value="disponible" <?= ($vehicule_a_modifier['statut'] === 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                    <option value="loué" <?= ($vehicule_a_modifier['statut'] === 'loué') ? 'selected' : ''; ?>>Loué</option>
                    <option value="maintenance" <?= ($vehicule_a_modifier['statut'] === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>

            <div class="form-group-admin">
                <label for="Image">Image du véhicule <?= $edit_mode ? "(Laisser vide pour conserver l'actuelle)" : "(Optionnel)"; ?></label>
                <input type="file" name="Image" id="Image" accept="image/*">
            </div>

            <button type="submit" name="sauvegarder_vehicule" class="btn-submit">
                <?= $edit_mode ? "Mettre à jour le véhicule" : "Enregistrer le véhicule"; ?>
            </button>
            
            <?php if ($edit_mode): ?>
                <a href="admin_vehicule.php" style="display:inline-block; margin-top:15px; text-decoration:none; color:#6b7280; font-size:14px;">Annuler la modification</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Liste des véhicules enregistrés -->
    <h3 class="section-title">Véhicules enregistrés dans le système</h3>
    
    <div class="vehicules-grid">
        <?php if (empty($vehicules)): ?>
            <p>Aucun véhicule enregistré pour le moment.</p>
        <?php else: ?>
            <?php foreach ($vehicules as $v): ?>
                <div class="vehicule-card">
                    <?php if (!empty($v['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($v['image']); ?>" alt="Photo du véhicule" class="vehicule-img">
                    <?php else: ?>
                        <div class="no-image">Pas d'image</div>
                    <?php endif; ?>

                    <h4><?= htmlspecialchars(($v['marque'] ?? '') . ' ' . ($v['modele'] ?? '')); ?></h4>
                    <div class="vehicule-info">Immat : <strong><?= htmlspecialchars($v['immatriculation'] ?? 'N/C'); ?></strong></div>
                    <div class="vehicule-info">Statut : <em><?= htmlspecialchars($v['statut'] ?? 'disponible'); ?></em></div>
                    <div class="vehicule-price"><?= number_format($v['prix_par_jour'] ?? 0, 0, ',', ' '); ?> Ar / jour</div>
                    
                    <!-- Boutons Modifier / Supprimer -->
                    <div class="vehicule-actions" style="padding: 0 15px 15px 15px; display: flex; gap: 10px;">
                        <a href="admin_vehicule.php?action=edit&id=<?= $v['id']; ?>" class="btn-edit" style="flex:1; text-align:center; background:#3b82f6; color:white; padding:8px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600;">Modifier</a>
                        <a href="admin_vehicule.php?action=delete&id=<?= $v['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');" style="flex:1; text-align:center; background:#ef4444; color:white; padding:8px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600;">Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php 
// Inclusion du footer
include 'includes/footer.php'; 
?>