<?php
/**
 * Fichier : mes_reservations.php
 * Rôle : Afficher la liste de toutes les réservations de l'utilisateur
 */

// 1. Inclusion de la connexion à la base de données
require_once 'config/db.php';

// 2. Définition de l'utilisateur actuel
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    // Si l'utilisateur n'est pas connecté, on le redirige vers le login
    header("Location: login.php");
    exit();
}

// 3. Requête SQL pour récupérer les réservations et les lier aux informations des véhicules
$sql = "SELECT r.*, v.marque, v.modele, v.immatriculation 
        FROM reservations r 
        JOIN vehicles v ON r.vehicle_id = v.id 
        WHERE r.user_id = ? 
        ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();

// 4. Inclusion de l'en-tête du site (header)
require_once 'includes/header.php';
?>

<!-- Liaison du CSS spécifique à cette page -->
<link rel="stylesheet" href="assets/css/mes_reservation.css">

<main>
    <!-- En-tête de la page -->
    <div class="reservations-header">
        <h1>Mes Réservations</h1>
        <p>Suivez l'état de vos locations de véhicules en cours et passées.</p>
        <p>Vous avez actuellement <strong><?= count($reservations); ?></strong> réservation(s) enregistrée(s).</p>
    </div>

    <?php if (empty($reservations)): ?>
        <!-- Si l'utilisateur n'a aucune réservation -->
        <div class="no-reservations">
            <p>Vous n'avez effectué aucune réservation pour le moment.</p>
            <a href="index.php" class="btn-primary">Découvrir nos véhicules</a>
        </div>
    <?php else: ?>
        <!-- Si l'utilisateur a des réservations, on les affiche dans une grille -->
        <div class="reservations-grid">
            <?php foreach ($reservations as $res): ?>
                <div class="reservation-card">
                    <div>
                        <!-- En-tête de la carte : Véhicule et Badge de statut -->
                        <div class="reservation-top">
                            <div>
                                <span style="font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700; display: block; margin-bottom: 4px;">Véhicule</span>
                                <h3 class="car-name"><?= htmlspecialchars($res['marque'] . ' ' . $res['modele']); ?></h3>
                                <span style="font-size: 12px; color: #6b7280;">Immat : <?= htmlspecialchars($res['immatriculation']); ?></span>
                            </div>
                            <?php if ($res['statut_reservation'] === 'confirmee'): ?>
                            <span class="badge-status badge-confirmee">Confirmée</span>
                            <?php elseif ($res['statut_reservation'] === 'annulee'): ?>
                            <span class="badge-status badge-annulee">Annulée</span>
                            <?php else: ?>
                            <span class="badge-status badge-attente">En attente</span>
                            <?php endif; ?>
                            
                        </div>

                        <!-- Dates de location -->
                        <div class="reservation-dates">
                            📅 Du <strong><?= htmlspecialchars($res['date_debut']); ?></strong> au <strong><?= htmlspecialchars($res['date_fin']); ?></strong>
                        </div>

                        <!-- Détails financiers et trajets -->
                        <div class="reservation-details">
                            📍 Départ : <strong><?= htmlspecialchars($res['ville_depart']); ?></strong><br>
                            🏁 Restitution : <strong><?= htmlspecialchars($res['ville_restitution']); ?></strong><br>
                            💰 Acompte demandé : <?= number_format($res['montant_acompte'], 0, ',', ' '); ?> Ar
                        </div>
                    </div>

                    <!-- Pied de la carte : Montant Total -->
                    <div style="border-top: 1px solid #f3f4f6; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 12px; color: #6b7280; text-transform: uppercase; font-weight: 700;">Total</span>
                        <span class="price-tag"><?= number_format($res['montant_total'], 0, ',', ' '); ?> Ar</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php
// 5. Inclusion du pied de page du site (footer)
require_once 'includes/footer.php';
?>