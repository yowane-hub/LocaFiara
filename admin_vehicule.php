<?php
// Connexion centralisée
require_once 'config/db.php';

$message = "";
$error = "";

// Traitement de l'ajout d'un véhicule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_vehicule'])) {
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $immatriculation = trim($_POST['immatriculation'] ?? '');
    $prix_par_jour = trim($_POST['prix_par_jour'] ?? '');
    $statut = $_POST['statut'] ?? 'disponible';
    $category_id = $_POST['category_id'] ?? 1;

    if (!empty($marque) && !empty($modele) && !empty($immatriculation) && !empty($prix_par_jour)) {
        
        // Gestion de l'image en binaire (LONGBLOB)
        $image_data = null;
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
            $image_data = file_get_contents($_FILES['Image']['tmp_name']);
        }

        try {
            $sql = "INSERT INTO vehicles (category_id, immatriculation, marque, modele, prix_par_jour, statut, image) 
                    VALUES (:category_id, :immatriculation, :marque, :modele, :prix_par_jour, :statut, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':immatriculation', $immatriculation);
            $stmt->bindParam(':marque', $marque);
            $stmt->bindParam(':modele', $modele);
            $stmt->bindParam(':prix_par_jour', $prix_par_jour);
            $stmt->bindParam(':statut', $statut);
            $stmt->bindParam(':image', $image_data, PDO::PARAM_LOB);

            $stmt->execute();
            $message = "Véhicule ajouté avec succès !";
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

// Inclusion du header de ton site
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

    <!-- Formulaire d'ajout structuré selon ton CSS -->
    <div class="form-card">
        <h3>Ajouter un nouveau véhicule</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-group-admin">
                <label for="marque">Marque</label>
                <input type="text" name="marque" id="marque" required>
            </div>

            <div class="form-group-admin">
                <label for="modele">Modèle</label>
                <input type="text" name="modele" id="modele" required>
            </div>

            <div class="form-group-admin">
                <label for="immatriculation">Immatriculation</label>
                <input type="text" name="immatriculation" id="immatriculation" required>
            </div>

            <div class="form-group-admin">
                <label for="prix_par_jour">Prix par jour (Ar)</label>
                <input type="number" name="prix_par_jour" id="prix_par_jour" required>
            </div>

            <div class="form-group-admin">
                <label for="statut">Statut</label>
                <select name="statut" id="statut">
                    <option value="disponible">Disponible</option>
                    <option value="loué">Loué</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div class="form-group-admin">
                <label for="Image">Image du véhicule (Optionnel)</label>
                <input type="file" name="Image" id="Image" accept="image/*">
            </div>

            <button type="submit" name="ajouter_vehicule" class="btn-submit">Enregistrer le véhicule</button>
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
                    <!-- Affichage de l'image convertie depuis le binaire (Base64) -->
                    <?php if (!empty($v['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($v['image']); ?>" alt="Photo du véhicule" class="vehicule-img">
                    <?php else: ?>
                        <div class="no-image">Pas d'image</div>
                    <?php endif; ?>

                    <h4><?= htmlspecialchars(($v['marque'] ?? '') . ' ' . ($v['modele'] ?? '')); ?></h4>
                    <div class="vehicule-info">Immat : <strong><?= htmlspecialchars($v['immatriculation'] ?? 'N/C'); ?></strong></div>
                    <div class="vehicule-info">Statut : <em><?= htmlspecialchars($v['statut'] ?? 'disponible'); ?></em></div>
                    <div class="vehicule-price"><?= number_format($v['prix_par_jour'] ?? 0, 0, ',', ' '); ?> Ar / jour</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php 
// Inclusion du footer de ton site
include 'includes/footer.php'; 
?>