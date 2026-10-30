<?php
/**
 * Fichier : reservation.php
 * Rôle : Page de réservation en deux colonnes larges et aérées
 */

// 1. Connexion à la base de données
require_once 'config/db.php';

// 2. Sécurité : Vérifier si l'ID du véhicule est présent et valide dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$vehicle_id = (int)$_GET['id'];

// 3. Récupération des informations du véhicule
$sql = "SELECT v.*, c.nom AS categorie_nom 
        FROM vehicles v 
        JOIN categories c ON v.category_id = c.id 
        WHERE v.id = ? AND v.statut = 'disponible'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

// Redirection si le véhicule n'existe pas
if (!$vehicle) {
    header('Location: index.php');
    exit();
}

// 4. Inclusion de l'en-tête
require_once 'includes/header.php';
?>

<!-- Liaison du CSS et du JS spécifiques à la réservation -->
<link rel="stylesheet" href="assets/css/reservation.css">

<main>
    <!-- En-tête -->
    <div class="res-header">
        <h1>Finaliser votre réservation</h1>
        <p>Vérifiez les détails de votre véhicule et configurez votre séjour en toute sérénité.</p>
    </div>

    <!-- 2 Colonnes larges et fluides -->
    <div class="res-grid">
        
        <!-- COLONNE 1 : Carte récapitulative du véhicule -->
        <div class="res-card">
            <div>
                <div class="res-car-img">
                    🚗 <?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?>
                </div>
                <span class="res-badge"><?= htmlspecialchars($vehicle['categorie_nom']); ?></span>
                <h2 class="res-title"><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h2>
                <p class="res-plate">Immatriculation : <strong><?= htmlspecialchars($vehicle['immatriculation']); ?></strong></p>
            </div>
            
            <div class="res-price-box">
                <div>
                    <span class="price-unit">Tarif journalier</span>
                    <span class="price-amount"><?= number_format($vehicle['prix_par_jour'], 0, ',', ' '); ?> Ar</span>
                </div>
            </div>
        </div>

        <!-- COLONNE 2 : Formulaire de réservation -->
        <div class="res-card">
            <div>
                <h3 class="form-title">Détails de la réservation</h3>
                
                <form action="traitement_reservation.php" method="POST" id="reservationForm">
                    <!-- ID caché du véhicule -->
                    <input type="hidden" name="vehicle_id" value="<?= $vehicle['id']; ?>">

                    <div class="form-group">
                        <label for="date_debut">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="date_fin">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" required class="form-control">
                    </div>

                    <div class="checkbox-container">
                        <input type="checkbox" name="avec_chauffeur" id="avec_chauffeur">
                        <label for="avec_chauffeur">Ajouter un chauffeur (+ 20 000 Ar / jour)</label>
                    </div>

                    <button type="submit" class="btn-submit">
                        Confirmer la réservation
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Lien de retour -->
    <div>
        <a href="index.php" class="back-link">
            ← Retour au catalogue des véhicules
        </a>
    </div>
</main>

<!-- Inclusion du script JavaScript de validation -->
<script src="assets/js/reservation.js"></script>

<?php
// 5. Inclusion du pied de page
require_once 'includes/footer.php';
?>