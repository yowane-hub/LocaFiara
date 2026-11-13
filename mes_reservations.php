<?php
/**
 * Fichier : mes_reservations.php
 * Rôle : Affiche l'historique et le statut des réservations de l'utilisateur connecté,
 *        avec les détails du véhicule, le chauffeur assigné et la possibilité de laisser un avis.
 */

// Démarrage de la session pour identifier l'utilisateur
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : Redirection vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Inclusion du fichier de connexion à la base de données (PDO)
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Récupération des réservations de l'utilisateur connecté avec jointure sur les véhicules et les chauffeurs
$sql = "SELECT r.*, v.marque, v.modele, v.immatriculation, 
               d.nom AS driver_nom, d.prenom AS driver_prenom 
        FROM reservations r 
        JOIN vehicles v ON r.vehicle_id = v.id 
        LEFT JOIN drivers d ON r.driver_id = d.id 
        WHERE r.user_id = ? 
        ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inclusion de l'en-tête de la page
require_once 'includes/header.php';
?>

<!-- Inclusion de la feuille de style externe -->
<link rel="stylesheet" href="assets/css/mes_reservation.css">

<main class="reservations-main">
    <div class="reservations-header">
        <h1>Mes Réservations</h1>
        <p>Retrouvez l'historique et le statut de toutes vos locations à Antsirabe.</p>
    </div>

    <?php if (empty($reservations)): ?>
        <!-- Message si aucune réservation n'est trouvée -->
        <div class="empty-reservations">
            <p>Vous n'avez pas encore effectué de réservation.</p>
            <a href="index.php" class="btn-primary">Découvrir nos véhicules</a>
        </div>
    <?php else: ?>
        <div class="reservations-list">
            <?php foreach ($reservations as $res): ?>
                <div class="reservation-card">

                    
                    
                    <!-- Colonne gauche : Détails du véhicule, dates et chauffeur -->
                    <div class="reservation-info">
                        <h3 class="reservation-title">
                            <?= htmlspecialchars($res['marque'] . ' ' . $res['modele']); ?>
                        </h3>
                        <p class="reservation-plate">
                            Immatriculation : <strong><?= htmlspecialchars($res['immatriculation']); ?></strong>
                        </p>
                        <p class="reservation-dates">
                            Du : <strong><?= htmlspecialchars($res['date_debut']); ?></strong> Au : <strong><?= htmlspecialchars($res['date_fin']); ?></strong>
                        </p>

                        <!-- Affichage du chauffeur assigné -->
                        <?php if (!empty($res['driver_nom'])): ?>
                            <div class="driver-badge">
                                👨‍✈️ Chauffeur : <strong><?= htmlspecialchars($res['driver_nom'] . ' ' . $res['driver_prenom']); ?></strong>
                            </div>
                        <?php else: ?>
                            <div class="no-driver-badge">
                                🚗 Sans chauffeur
                            </div>
                        <?php endif; ?>

                        <!-- Formulaire d'avis client (s'affiche uniquement si la réservation est confirmée) -->
                        <?php if ($res['statut_reservation'] === 'confirmee'): ?>
                            <!-- Bouton pour accéder au paiement -->
                            <div class="payment-action-box" style="margin-bottom: 15px;">
                                <a href="payer.php?reservation_id=<?= $res['id']; ?>" class="btn-pay-link">
                                    💳 Procéder au paiement
                                </a>
                            </div>  

                            <div class="reservation-actions">
                                <!-- Si la réservation est confirmée et qu'il n'y a pas encore de paiement validé -->
                                <?php if ($res['statut_reservation'] === 'confirmee'): ?>
                                    <a href="payer.php?reservation_id=<?= $res['id']; ?>" class="btn-pay-action">Procéder au paiement</a>
                                <?php endif; ?>

                                <!-- Vérification et affichage du bouton de téléchargement de la facture -->
                                <?php
                                $stmt_inv = $pdo->prepare("SELECT id FROM invoices WHERE reservation_id = ?");
                                $stmt_inv->execute([$res['id']]);
                                $facture = $stmt_inv->fetch(PDO::FETCH_ASSOC);
                                ?>

                                <?php if ($facture): ?>
                                    <a href="telecharger_facture.php?id=<?= $facture['id']; ?>" class="btn-download-invoice" target="_blank">
                                        📄 Télécharger ma facture (PDF)
                                    </a>
                                <?php endif; ?>
                            </div>
                                        

                            <div class="review-box">
                                <form action="traitement_avis.php" method="POST" class="review-form">
                                    <input type="hidden" name="reservation_id" value="<?= $res['id']; ?>">
                                    
                                    <select name="note" required class="review-select">
                                        <option value="">-- Note --</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                        <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                        <option value="3">⭐⭐⭐ (3/5)</option>
                                        <option value="2">⭐⭐ (2/5)</option>
                                        <option value="1">⭐ (1/5)</option>
                                    </select>

                                    <input type="text" name="commentaire" placeholder="Votre avis sur cette location..." required class="review-input">

                                    <button type="submit" class="review-btn">
                                        Envoyer un avis
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Colonne droite : Statut et Montant total -->
                    <div class="reservation-meta">
                        <?php 
                            // Attribution de la classe CSS selon le statut
                            $statut_class = 'status-en_attente';
                            if ($res['statut_reservation'] === 'confirmee') {
                                $statut_class = 'status-confirmee';
                            } elseif ($res['statut_reservation'] === 'annulee') {
                                $statut_class = 'status-annulee';
                            }
                        ?>
                        <span class="status-badge <?= $statut_class; ?>">
                            <?= htmlspecialchars($res['statut_reservation']); ?>
                        </span>
                        <div class="reservation-price">
                            <?= number_format($res['montant_total'], 0, ',', ' '); ?> Ar
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="back-link-container">
        <a href="index.php" class="back-link">← Retour au catalogue des véhicules</a>
    </div>
</main>

<?php 
// Inclusion du pied de page
require_once 'includes/footer.php';
?>