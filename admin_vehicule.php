<?php
/**
 * Fichier : admin_vehicules.php
 * Rôle : Gestion (Ajout / Liste) des véhicules connectée à ta vraie structure de table
 */

session_start();
require_once 'config/db.php';

// Sécurité : Vérifier si l'utilisateur est bien admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message_succes = "";
$message_erreur = "";

// Traitement de l'ajout d'un véhicule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $immatriculation = trim($_POST['immatriculation'] ?? '');
    $prix_par_jour = trim($_POST['prix_par_jour'] ?? '');

    if (empty($marque) || empty($modele) || empty($immatriculation) || empty($prix_par_jour)) {
        $message_erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $image_name = null;
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['Image']['tmp_name'];
            $file_name = basename($_FILES['Image']['name']);
            $upload_dir = 'assets/img/vehicules/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                $image_name = $file_name;
            }
        }

        // Insertion avec les noms exacts de tes colonnes MySQL
        $sql = "INSERT INTO vehicles (marque, modele, Immatriculation, prix_par_jour, Image, statut) VALUES (?, ?, ?, ?, ?, 'disponible')";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$marque, $modele, $immatriculation, $prix_par_jour, $image_name])) {
            $message_succes = "Véhicule ajouté avec succès !";
        } else {
            $message_erreur = "Erreur lors de l'ajout du véhicule.";
        }
    }
}

// Récupération de la liste des véhicules depuis ta table
$vehicules = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC")->fetchAll();

require_once 'includes/header.php';
?>

<!-- Liaison avec le fichier de style externe -->
<link rel="stylesheet" href="assets/css/admin_vehicule.css">

<main class="admin-main">
    <h1>Gestion des Véhicules</h1>
    <a href="admin_dashboard.php" class="back-link">← Retour au tableau de bord</a>

    <?php if (!empty($message_succes)): ?>
        <div class="alert-success"><?= htmlspecialchars($message_succes); ?></div>
    <?php endif; ?>

    <?php if (!empty($message_erreur)): ?>
        <div class="alert-error"><?= htmlspecialchars($message_erreur); ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout adapté à ta base de données -->
    <div class="form-card">
        <h3>Ajouter un nouveau véhicule</h3>
        <form action="admin_vehicules.php" method="POST" enctype="multipart/form-data">
            <div class="form-group-admin">
                <label for="marque">Marque</label>
                <input type="text" name="marque" id="marque" required placeholder="Ex: Toyota">
            </div>
            <div class="form-group-admin">
                <label for="modele">Modèle</label>
                <input type="text" name="modele" id="modele" required placeholder="Ex: Hilux Revo 4x4">
            </div>
            <div class="form-group-admin">
                <label for="Immatriculation">Immatriculation</label>
                <input type="text" name="Immatriculation" id="Immatriculation" required placeholder="Ex: 1234 TAB">
            </div>
            <div class="form-group-admin">
                <label for="prix_par_jour">Prix par jour (Ar)</label>
                <input type="number" name="prix_par_jour" id="prix_par_jour" required placeholder="Ex: 180000">
            </div>
            <div class="form-group-admin">
                <label for="Image">Nom du fichier image (Optionnel)</label>
                <input type="file" name="Image" id="Image" accept="image/*">
            </div>
            <button type="submit" class="btn-submit">Enregistrer le véhicule</button>
        </form>
    </div>

    <!-- Liste des véhicules actuels -->
    <h3 class="section-title">Véhicules enregistrés dans le système</h3>
    <div class="vehicules-grid">
        <?php foreach ($vehicules as $v): ?>
            <div class="vehicule-card">
                <h4><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']); ?></h4>
                <div class="vehicule-info">Immat : <strong><?= htmlspecialchars($v['Immatriculation']); ?></strong></div>
                <div class="vehicule-info">Statut : <em><?= htmlspecialchars($v['statut'] ?? 'disponible'); ?></em></div>
                <div class="vehicule-price"><?= number_format($v['prix_par_jour'], 0, ',', ' '); ?> Ar / jour</div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>