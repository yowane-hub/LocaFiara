<?php
/**
 * Fichier : reservation.php
 * Rôle : Page de réservation avec calcul automatique de la durée et du prix
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$vehicle_id = $_GET['id'] ?? null;

if (!$vehicle_id || !is_numeric($vehicle_id)) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT v.*, c.nom AS categorie_nom 
        FROM vehicles v 
        LEFT JOIN categories c ON v.category_id = c.id 
        WHERE v.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    header("Location: index.php");
    exit();
}

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/reservation.css">

<main>
    <div class="res-header">
        <h1>Finaliser votre réservation</h1>
        <p>Vérifiez les détails de votre véhicule et configurez votre séjour en toute sérénité.</p>
    </div>

    <div class="res-grid">
        
        <!-- COLONNE 1 : Carte récapitulative du véhicule -->
        <div class="res-card">
            <div>
                <div class="res-car-img">
                    🚗
                </div>
                <span class="res-badge"><?= htmlspecialchars($vehicle['categorie_nom'] ?? 'Véhicule'); ?></span>
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
            <h3 class="form-title">Détails de la réservation</h3>
            
            <script>
                // Passage sécurisé du tarif journalier en nombre brut pour JavaScript
                window.prixVehiculeJour = <?= floatval($vehicle['prix_par_jour']); ?>;
                console.log("Prix véhicule chargé :", window.prixVehiculeJour);
            </script>

            <form action="traitement_reservation.php" method="POST" id="reservationForm">
                
                <input type="hidden" name="vehicle_id" value="<?= $vehicle['id']; ?>">

                <div class="form-group">
                    <label for="date_debut">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" required class="form-control">
                </div>

                <!-- Affichage de la durée en jours -->
                <div id="affichage_duree" style="font-size: 14px; color: #4b5563; margin-bottom: 15px; font-weight: 600;">
                    Durée : Sélectionnez les dates
                </div>

                <!-- Option Chauffeur -->
                <div class="checkbox-container" style="margin: 20px 0; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="avec_chauffeur" id="avec_chauffeur" style="width: 18px; height: 18px;">
                    <label for="avec_chauffeur" style="font-weight: 600; font-size: 14px; color: #374151; cursor: pointer;">
                        Ajouter un chauffeur (+ 20 000 Ar / jour)
                    </label>
                </div>

                <!-- Bloc visuel du prix total -->
                <div style="background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 700; display: block;">Estimation Totale :</span>
                    <span id="prix_total_affichage" style="font-size: 22px; font-weight: 800; color: #1e3a8a;">0 Ar</span>
                </div>

                <button type="submit" class="btn-submit">
                    Confirmer la réservation
                </button>
            </form>
        </div>

    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a href="index.php" class="back-link">← Retour au catalogue des véhicules</a>
    </div>
</main>

<!-- Inclusion du script JavaScript de calcul en direct -->
<script src="assets/js/reservation.js"></script>

<?php 
require_once 'includes/footer.php';
?>