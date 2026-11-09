<?php
/**
 * Fichier : admin_dashboard.php
 * Rôle : Tableau de bord administrateur pour la gestion globale des réservations et des accès rapides.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
require_once 'includes/admin_header.php';
?>

<!-- Lien vers la feuille de style dédiée -->
<link rel="stylesheet" href="assets/css/admin_dashboard.css">

<?php
$message_succes = "";

// 1. Traitement de la modification du statut lorsqu'on clique sur "Mettre à jour"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['nouveau_statut'])) {
    $res_id = $_POST['reservation_id'];
    $nouveau_statut = $_POST['nouveau_statut'];

    $update_sql = "UPDATE reservations SET statut_reservation = ? WHERE id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$nouveau_statut, $res_id]);

    $message_succes = "Le statut de la réservation #$res_id a été mis à jour avec succès !";
}

// 2. Récupération de toutes les réservations avec les infos clients et véhicules
$sql = "SELECT r.*, v.marque, v.modele, v.immatriculation, u.nom AS client_nom, u.email AS client_email, 
               d.nom AS driver_nom, d.prenom AS driver_prenom 
        FROM reservations r 
        JOIN vehicles v ON r.vehicle_id = v.id 
        JOIN users u ON r.user_id = u.id 
        LEFT JOIN drivers d ON r.driver_id = d.id 
        ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$toutes_reservations = $stmt->fetchAll();

// 3. Calcul de quelques statistiques rapides pour l'admin
$total_reservations = count($toutes_reservations);
$chiffre_affaires = array_sum(array_column($toutes_reservations, 'montant_total'));
?>

<main class="admin-dashboard-main">
    <!-- En-tête de la page -->
    <div class="res-header">
        <h1>Espace Administrateur</h1>
        <p>Pilotez l'ensemble des activités et suivez les locations en temps réel à Antsirabe.</p>
    </div>

    <!-- Liens d'accès rapide vers les modules de gestion (Véhicules & Chauffeurs) -->
    <div class="quick-nav-bar">
        <a href="admin_vehicule.php" class="quick-btn vehicle-btn">
            🚗 Gérer les Véhicules
        </a>
        <a href="admin_drivers.php" class="quick-btn driver-btn">
            👨‍✈️ Gérer les Chauffeurs
        </a>
    </div>

    <!-- Notification de succès -->
    <?php if (!empty($message_succes)): ?>
        <div class="alert-success-dash">
            <?= htmlspecialchars($message_succes); ?>
        </div>
    <?php endif; ?>

    <!-- Mini-barre de statistiques professionnelles -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Réservations</span>
            <span class="stat-value"><?= $total_reservations; ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Chiffre d'Affaires Global</span>
            <span class="stat-value"><?= number_format($chiffre_affaires, 0, ',', ' '); ?> Ar</span>
        </div>
    </div>

    <!-- Liste des réservations -->
    <?php if (empty($toutes_reservations)): ?>
        <div class="empty-card">
            <p>Aucune réservation enregistrée pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="reservations-list">
            <?php foreach ($toutes_reservations as $res): ?>
                <?php 
                // Définition dynamique des couleurs de badge selon le statut
                $statut = $res['statut_reservation'];
                $badge_class = 'badge-attente'; // Par défaut (en attente)
                if ($statut === 'confirmee') { $badge_class = 'badge-confirmee'; }
                if ($statut === 'annulee') { $badge_class = 'badge-annulee'; }
                ?>

                <div class="reservation-item">
                    <div class="reservation-info-group">
                        <div class="client-badge-row">
                            <span class="client-tag">
                                Client : <?= htmlspecialchars($res['client_nom'] ?? $res['client_email']); ?>
                            </span>
                            <span class="status-badge <?= $badge_class; ?>">
                                <?= htmlspecialchars($statut); ?>
                            </span>
                        </div>
                        
                        <h3 class="res-car-title">
                            <?= htmlspecialchars($res['marque']); ?> <?= htmlspecialchars($res['modele']); ?>
                            <span class="res-immat">(<?= htmlspecialchars($res['immatriculation']); ?>)</span>
                        </h3>
                        
                        <p class="res-dates">
                            Du : <strong><?= htmlspecialchars($res['date_debut']); ?></strong> Au : <strong><?= htmlspecialchars($res['date_fin']); ?></strong>
                        </p>

                        <?php if (!empty($res['driver_nom'])): ?>
                            <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">
                                👨‍✈️ Chauffeur assigné : <strong><?= htmlspecialchars($res['driver_nom'] . ' ' . $res['driver_prenom']); ?></strong>
                            </p>
                        <?php else: ?>
                            <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">
                                🚗 Sans chauffeur (Autonome)
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Montant et formulaire de mise à jour du statut -->
                    <div class="reservation-action-group">
                        <div class="res-amount">
                            <?= number_format($res['montant_total'], 0, ',', ' '); ?> Ar
                        </div>

                        <form action="admin_dashboard.php" method="POST" class="status-form">
                            <input type="hidden" name="reservation_id" value="<?= $res['id']; ?>">
                            <select name="nouveau_statut" class="status-select">
                                <option value="en attente" <?= ($statut === 'en attente') ? 'selected' : ''; ?>>En attente</option>
                                <option value="confirmee" <?= ($statut === 'confirmee') ? 'selected' : ''; ?>>Confirmée</option>
                                <option value="annulee" <?= ($statut === 'annulee') ? 'selected' : ''; ?>>Annulée</option>
                            </select>
                            <button type="submit" class="btn-update-status">
                                Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="back-link-container">
        <a href="index.php" class="back-link">← Retour au site principal</a>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>