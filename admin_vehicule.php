<?php
/*
 * Fichier : admin_vehicule.php
 * Rôle : Gestion des véhicules (Ajout, Modification, Suppression)
 */
require_once 'config/db.php';

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

$message = "";
$error = "";

// Récupération dynamique de la liste des catégories
$stmt_cat = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories_liste = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// 1. Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_del = intval($_GET['id']);
    try {
        $stmt_del = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
        $stmt_del->execute([$id_del]);
        $message = "Véhicule supprimé avec succès !";
    } catch (PDOException $e) {
        $error = "Impossible de supprimer ce véhicule (il est lié à une réservation).";
    }
}

// 2. Récupération des données en mode édition
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

// 3. Traitement du formulaire (Création ou Mise à jour)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sauvegarder_vehicule'])) {
    $smarque = trim($_POST['marque'] ?? '');
    $smodele = trim($_POST['modele'] ?? '');
    $simmatriculation = trim($_POST['immatriculation'] ?? '');
    $sprix_par_jour = trim($_POST['prix_par_jour'] ?? '');
    $sstatut = $_POST['statut'] ?? 'disponible';
    $scategory_id = intval($_POST['category_id'] ?? 1);
    $sid = $_POST['id_vehicule'] ?? '';

    if (!empty($smarque) && !empty($smodele) && !empty($simmatriculation) && !empty($sprix_par_jour)) {
        $image_data = null;
        $update_image_sql = "";
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
            $image_data = file_get_contents($_FILES['Image']['tmp_name']);
            $update_image_sql = ", image = :image";
        }

        try {
            if (!empty($sid)) {
                $sql = "UPDATE vehicles SET category_id = :category_id, immatriculation = :immatriculation, 
                        marque = :marque, modele = :modele, prix_par_jour = :prix_par_jour, statut = :statut $update_image_sql 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $sid, PDO::PARAM_INT);
            } else {
                $sql = "INSERT INTO vehicles (category_id, immatriculation, marque, modele, prix_par_jour, statut, image) 
                        VALUES (:category_id, :immatriculation, :marque, :modele, :prix_par_jour, :statut, :image)";
                $stmt = $pdo->prepare($sql);
            }

            $stmt->bindValue(':category_id', $scategory_id, PDO::PARAM_INT);
            $stmt->bindValue(':immatriculation', $simmatriculation);
            $stmt->bindValue(':marque', $smarque);
            $stmt->bindValue(':modele', $smodele);
            $stmt->bindValue(':prix_par_jour', $sprix_par_jour);
            $stmt->bindValue(':statut', $sstatut);

            if ($image_data !== null) {
                $stmt->bindValue(':image', $image_data, PDO::PARAM_LOB);
            }

            $stmt->execute();
            $message = !empty($sid) ? "Véhicule mis à jour avec succès !" : "Véhicule ajouté avec succès !";
            
            $edit_mode = false;
            $vehicule_a_modifier = [
                'id' => '', 'marque' => '', 'modele' => '', 'immatriculation' => '', 
                'prix_par_jour' => '', 'statut' => 'disponible', 'category_id' => 1
            ];
        } catch (PDOException $e) {
            $error = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Récupération de la liste des véhicules avec leurs catégories
$stmt_veh = $pdo->query("SELECT v.*, c.nom AS categorie_nom FROM vehicles v LEFT JOIN categories c ON v.category_id = c.id ORDER BY v.id DESC");
$vehicules = $stmt_veh->fetchAll(PDO::FETCH_ASSOC);

// Inclusion de l'en-tête admin unifié
include 'includes/admin_header.php';
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

    <!-- Formulaire d'ajout / modification -->
    <div class="form-card">
        <h3><?= $edit_mode ? "Modifier le véhicule #" . $vehicule_a_modifier['id'] : "Ajouter un nouveau véhicule"; ?></h3>
        <form action="" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id_vehicule" value="<?= htmlspecialchars($vehicule_a_modifier['id']); ?>">

            <div class="form-group-admin">
                <label for="category_id">Catégorie du véhicule</label>
                <select name="category_id" id="category_id" required>
                    <?php foreach ($categories_liste as $cat): ?>
                        <option value="<?= $cat['id']; ?>" <?= ($vehicule_a_modifier['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

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
                <a href="admin_vehicule.php" style="display: inline-block; margin-top: 15px; text-decoration: none; color: #6b7280; font-size: 14px;">Annuler la modification</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Liste des véhicules enregistrés -->
    <h3 class="section-title">Véhicules enregistrés dans le système</h3>

    <div class="vehicles-grid">
        <?php if (empty($vehicules)): ?>
            <p>Aucun véhicule enregistré pour le moment.</p>
        <?php else: ?>
            <?php foreach ($vehicules as $v): ?>
                <div class="vehicule-card">
                    <!-- Conteneur d'image propre -->
                    <div class="car-thumb">
                        <?php if (!empty($v['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($v['image']); ?>" alt="Photo du véhicule">
                        <?php else: ?>
                            <div class="no-image">Pas d'image</div>
                        <?php endif; ?>
                    </div>

                    <span class="car-badge">
                        <?= htmlspecialchars($v['categorie_nom'] ?? 'Général'); ?>
                    </span>

                    <h4><?= htmlspecialchars($v['marque'] ?? '') . ' ' . htmlspecialchars($v['modele'] ?? ''); ?></h4>
                    <div class="vehicule-info">Immat : <strong><?= htmlspecialchars($v['immatriculation'] ?? 'N/C'); ?></strong></div>
                    <div class="vehicule-info">Statut : <em><?= htmlspecialchars($v['statut'] ?? 'disponible'); ?></em></div>
                    <div class="vehicule-price"><?= number_format($v['prix_par_jour'] ?? 0, 0, ',', ' '); ?> Ar / jour</div>

                    <!-- Boutons Modifier / Supprimer -->
                    <div class="vehicule-actions">
                        <a href="admin_vehicule.php?action=edit&id=<?= $v['id']; ?>" class="btn-edit">Modifier</a>
                        <a href="admin_vehicule.php?action=delete&id=<?= $v['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>