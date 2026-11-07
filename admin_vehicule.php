<?php
/**
 * Fichier : admin_vehicule.php
 * Module complet de gestion des véhicules (Sécurité, Traitement POST, Upload sécurisé et Affichage)
 */

session_start();
require_once 'config/db.php';

// 1. Sécurité : Vérifier si l'utilisateur est bien admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message_succes = "";
$message_erreur = "";
$marque = '';
$modele = '';
$immatriculation = '';
$prix_par_jour = '';

// 2. Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $immatriculation = trim($_POST['immatriculation'] ?? '');
    $prix_par_jour = trim($_POST['prix_par_jour'] ?? '');
    $category_id = 1; // Valeur par défaut

    // Vérification des champs obligatoires
    if (empty($marque) || empty($modele) || empty($immatriculation) || empty($prix_par_jour)) {
        $message_erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $image_name = null;
        
        // Gestion sécurisée de l'upload de l'image
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['Image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['Image']['tmp_name'];
                $file_name = basename($_FILES['Image']['name']);
                
                // Nettoyage du nom pour éviter les conflits et caractères spéciaux
                $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file_name);
                
                $upload_dir = '/var/www/html/LocaFiara_img/vehicules/';
                
                // Création automatique du dossier s'il n'existe pas
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Déplacement du fichier vers le dossier définitif
                if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    $image_name = $file_name;
                } else {
                    $message_erreur = "Erreur : Impossible de sauvegarder l'image. Vérifiez les permissions du dossier 'assets/img/vehicules/'.";
                }
            } else {
                $message_erreur = "Erreur lors du transfert de l'image (Code d'erreur : " . $_FILES['Image']['error'] . ")";
            }
        }

        // S'il n'y a pas d'erreur d'upload, on procède à l'insertion dans la base de données
        if (empty($message_erreur)) {
            try {
                $sql = "INSERT INTO vehicles (category_id, marque, modele, immatriculation, prix_par_jour, Image, statut) VALUES (?, ?, ?, ?, ?, ?, 'disponible')";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$category_id, $marque, $modele, $immatriculation, $prix_par_jour, $image_name])) {
                    $message_succes = "Véhicule ajouté avec succès !";
                    // Réinitialisation des champs du formulaire
                    $marque = $modele = $immatriculation = $prix_par_jour = "";
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message_erreur = "Erreur : Cette immatriculation existe déjà dans la base de données.";
                } else {
                    $message_erreur = "Erreur SQL : " . $e->getMessage();
                }
            }
        }
    }
}

// 3. Récupération de la liste actualisée des véhicules
$stmt_vehicules = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC");
$vehicules = $stmt_vehicules->fetchAll();

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/admin_vehicule.css">

<main class="admin-main">
    <h1>Gestion des Véhicules</h1>
    <a href="admin_dashboard.php" class="back-link">← Retour au tableau de bord</a>

    <!-- Affichage des alertes -->
    <?php if (!empty($message_succes)): ?>
        <div class="alert-success"><?= htmlspecialchars($message_succes); ?></div>
    <?php endif; ?>

    <?php if (!empty($message_erreur)): ?>
        <div class="alert-error"><?= htmlspecialchars($message_erreur); ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <div class="form-card">
        <h3>Ajouter un nouveau véhicule</h3>
        <form action="admin_vehicule.php" method="POST" enctype="multipart/form-data">
            <div class="form-group-admin">
                <label for="marque">Marque</label>
                <input type="text" name="marque" id="marque" required placeholder="Ex: Toyota" value="<?= htmlspecialchars($marque); ?>">
            </div>
            <div class="form-group-admin">
                <label for="modele">Modèle</label>
                <input type="text" name="modele" id="modele" required placeholder="Ex: Hilux Revo 4x4" value="<?= htmlspecialchars($modele); ?>">
            </div>
            <div class="form-group-admin">
                <label for="immatriculation">Immatriculation</label>
                <input type="text" name="immatriculation" id="immatriculation" required placeholder="Ex: 1234 TAB" value="<?= htmlspecialchars($immatriculation); ?>">
            </div>
            <div class="form-group-admin">
                <label for="prix_par_jour">Prix par jour (Ar)</label>
                <input type="number" name="prix_par_jour" id="prix_par_jour" required placeholder="Ex: 180000" value="<?= htmlspecialchars($prix_par_jour); ?>">
            </div>
            <div class="form-group-admin">
                <label for="Image">Image du véhicule (Optionnel)</label>
                <input type="file" name="Image" id="Image" accept="image/*">
            </div>
            <button type="submit" class="btn-submit">Enregistrer le véhicule</button>
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
                    <h4><?= htmlspecialchars(($v['marque'] ?? '') . ' ' . ($v['modele'] ?? '')); ?></h4>
                    <div class="vehicule-info">Immat : <strong><?= htmlspecialchars($v['immatriculation'] ?? 'N/C'); ?></strong></div>
                    <div class="vehicule-info">Statut : <em><?= htmlspecialchars($v['statut'] ?? 'disponible'); ?></em></div>
                    <div class="vehicule-price"><?= number_format($v['prix_par_jour'] ?? 0, 0, ',', ' '); ?> Ar / jour</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>