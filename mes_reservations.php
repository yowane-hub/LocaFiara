<?php
/**
 * Fichier : mes_reservations.php
 * Rôle : Afficher l'historique des réservations de l'utilisateur connecté 
 *        en utilisant son propre fichier de style dédié (mes_reservations.css).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
require_once 'includes/header.php';
?>

<!-- Liaison spécifique avec le fichier CSS de mes_reservations -->
<link rel="stylesheet" href="assets/css/mes_reservation.css">

<?php
$user_id = $_SESSION['user_id'];

// Récupération des réservations de l'utilisateur avec les informations des véhicules
$sql = "SELECT r.*, v.marque, v.modele, v.immatriculation 
        FROM reservations r 
        JOIN vehicles v ON r.vehicle_id = v.id 
        WHERE r.user_id = ? 
        ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<main>
    <!-- En-tête de la page -->
    <div class="res-header">
        <h1>Mes Réservations</h1>
        <p>Retrouvez l'historique et le statut de toutes vos locations à Antsirabe.</p>
    </div>

    <?php if (empty($reservations)): ?>
        <!-- Bloc affiché si aucune réservation n'existe -->
        <div style="background: #ffffff; padding: 40px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6;">
            <p style="color: #6b7280; font-size: 16px; margin-bottom: 20px;">Vous n'avez effectué aucune réservation pour le moment.</p>
            <a href="index.php" class="btn-submit" style="display: inline-block; width: auto; padding: 12px 25px; text-decoration: none;">Louer un véhicule</a>
        </div>
    <?php else: ?>
        <!-- Liste des cartes de réservation -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($reservations as $res): ?>
                <div style="background: #ffffff; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    
                    <!-- Informations du véhicule et dates -->
                    <div>
                        <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 5px 0;">
                            <?= htmlspecialchars($res['marque'] . ' ' . $res['modele']); ?>
                        </h3>
                        <p style="font-size: 13px; color: #6b7280; margin: 0 0 10px 0;">
                            Immatriculation : <strong><?= htmlspecialchars($res['immatriculation']); ?></strong>
                        </p>
                        <p style="font-size: 14px; color: #374151; margin: 0; font-weight: 500;">
                            Du : <strong><?= htmlspecialchars($res['date_debut']); ?></strong> 
                            Au : <strong><?= htmlspecialchars($res['date_fin']); ?></strong>
                        </p>
                    </div>

                    <!-- Statut et montant total -->
                    <div style="text-align: right;">
                        <span style="display: inline-block; padding: 6px 12px; font-size: 12px; font-weight: 700; background: #fef3c7; color: #d97706; border-radius: 8px; margin-bottom: 8px; text-transform: uppercase;">
                            <?= htmlspecialchars($res['statut_reservation']); ?>
                        </span>
                        <div style="font-size: 22px; font-weight: 800; color: #1e3a8a;">
                            <?= number_format($res['montant_total'], 0, ',', ' '); ?> Ar
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Lien de retour au catalogue -->
    <div style="margin-top: 30px;">
        <a href="index.php" class="back-link">← Retour au catalogue des véhicules</a>
    </div>
</main>

<?php 
require_once 'includes/footer.php';
?>