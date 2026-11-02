<?php
/**
 * Fichier : admin_dashboard.php
 * Rôle : Tableau de bord administrateur professionnel pour la gestion globale 
 *        des réservations, des statuts et des indicateurs clés de LocaFiara.
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

<!-- Liaison avec la feuille de style dédiée -->
<link rel="stylesheet" href="assets/css/mes_reservation.css">
            
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
$sql = "SELECT r.*, v.marque, v.modele, v.immatriculation, u.nom AS client_nom, u.email AS client_email 
        FROM reservations r 
        JOIN vehicles v ON r.vehicle_id = v.id 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$toutes_reservations = $stmt->fetchAll();

// 3. Calcul de quelques statistiques rapides pour l'admin
$total_reservations = count($toutes_reservations);
$chiffre_affaires = array_sum(array_column($toutes_reservations, 'montant_total'));
?>

<main>
    <!-- En-tête de la page -->
    <div class="res-header">
        <h1>Espace Administrateur</h1>
        <p>Pilotez l'ensemble des activités et suivez les locations en temps réel à Antsirabe.</p>
    </div>

    <!-- Notification de succès si une mise à jour vient d'être faite -->
    <?php if (!empty($message_succes)): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 600; font-size: 14px; border: 1px solid #a7f3d0;">
            <?= htmlspecialchars($message_succes); ?>
        </div>
    <?php endif; ?>

    <!-- Mini-barre de statistiques professionnelles -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px;">
        <div style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #f3f4f6; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 5px;">Total Réservations</span>
            <span style="font-size: 24px; font-weight: 800; color: #111827;"><?= $total_reservations; ?></span>
        </div>
        <div style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #f3f4f6; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 5px;">Chiffre d'Affaires Global</span>
            <span style="font-size: 24px; font-weight: 800; color: #1e3a8a;"><?= number_format($chiffre_affaires, 0, ',', ' '); ?> Ar</span>
        </div>
    </div>

    <!-- Liste des réservations -->
    <?php if (empty($toutes_reservations)): ?>
        <div style="background: #ffffff; padding: 40px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6;">
            <p style="color: #6b7280; font-size: 16px; margin: 0;">Aucune réservation enregistrée pour le moment.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($toutes_reservations as $res): ?>
                
                <?php 
                    // Définition dynamique des couleurs de badge selon le statut
                    $statut = $res['statut_reservation'];
                    $badge_bg = "#fef3c7"; $badge_color = "#d97706"; // Par défaut (en_attente)
                    if ($statut == 'confirmee') { $badge_bg = "#d1fae5"; $badge_color = "#065f46"; }
                    if ($statut == 'annulee') { $badge_bg = "#fee2e2"; $badge_color = "#991b1b"; }
                ?>

                <div style="background: #ffffff; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    
                    <!-- Informations client et véhicule -->
                    <div>
                        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; background: #e0e7ff; color: #3730a3; padding: 4px 8px; border-radius: 6px;">
                                Client : <?= htmlspecialchars($res['client_nom'] ?? $res['client_email']); ?>
                            </span>
                            <!-- Badge de statut visuel -->
                            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; background: <?= $badge_bg; ?>; color: <?= $badge_color; ?>; padding: 4px 8px; border-radius: 6px;">
                                <?= htmlspecialchars($statut); ?>
                            </span>
                        </div>

                        <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 5px 0;">
                            <?= htmlspecialchars($res['marque'] . ' ' . $res['modele']); ?> 
                            <span style="font-size: 13px; font-weight: 500; color: #6b7280;">(<?= htmlspecialchars($res['immatriculation']); ?>)</span>
                        </h3>
                        <p style="font-size: 14px; color: #374151; margin: 0; font-weight: 500;">
                            Du : <strong><?= htmlspecialchars($res['date_debut']); ?></strong> 
                            Au : <strong><?= htmlspecialchars($res['date_fin']); ?></strong>
                        </p>
                    </div>

                    <!-- Montant et formulaire de mise à jour du statut -->
                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                        <div style="font-size: 22px; font-weight: 800; color: #1e3a8a;">
                            <?= number_format($res['montant_total'], 0, ',', ' '); ?> Ar
                        </div>

                        <form action="admin_dashboard.php" method="POST" style="display: flex; gap: 8px; align-items: center;">
                            <input type="hidden" name="reservation_id" value="<?= $res['id']; ?>">
                            <select name="nouveau_statut" class="form-control" style="padding: 6px 12px; font-size: 12px; border-radius: 8px; width: auto;">
                                <option value="en_attente" <?= ($statut == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                                <option value="confirmee" <?= ($statut == 'confirmee') ? 'selected' : ''; ?>>Confirmée</option>
                                <option value="annulee" <?= ($statut == 'annulee') ? 'selected' : ''; ?>>Annulée</option>
                            </select>
                            <button type="submit" style="background: #1e3a8a; color: white; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                                Mettre à jour
                            </button>
                        </form>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="margin-top: 30px;">
        <a href="index.php" class="back-link">← Retour au site principal</a>
    </div>
</main>

<?php 
require_once 'includes/footer.php';
?>