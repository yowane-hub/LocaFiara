<?php
/**
 * Fichier : admin_paiements.php
 * Rôle : Tableau de bord admin pour vérifier les références Mobile Money, valider les paiements,
 *        générer la facture et notifier le client par e-mail.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification des droits administrateur (adapte selon le nom de ta variable de session admin)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$message = '';

// Traitement de la validation ou du refus d'un paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['payment_id'])) {
    $payment_id = (int)$_POST['payment_id'];
    $action = $_POST['action']; // 'valide' ou 'refuse'

    if (in_array($action, ['valide', 'refuse'])) {
        try {
            // Récupérer les infos complètes du paiement, de la réservation et du client
            $stmt_info = $pdo->prepare("
                SELECT p.*, r.id AS res_id, r.date_debut, u.nom, u.prenom, u.email, v.marque, v.modele 
                FROM payments p
                JOIN reservations r ON p.reservation_id = r.id
                JOIN users u ON r.user_id = u.id
                JOIN vehicles v ON r.vehicle_id = v.id
                WHERE p.id = ?
            ");
            $stmt_info->execute([$payment_id]);
            $pay = $stmt_info->fetch(PDO::FETCH_ASSOC);

            if ($pay) {
                // 1. Mettre à jour le statut du paiement
                $stmt_up = $pdo->prepare("UPDATE payments SET statut_paiement = ? WHERE id = ?");
                $stmt_up->execute([$action, $payment_id]);

                if ($action === 'valide') {
                    // 2. Générer la facture officielle dans 'invoices'
                    $numero_facture = 'FACT-' . date('Y') . '-' . str_pad($pay['res_id'], 4, '0', STR_PAD_LEFT);
                    $stmt_inv = $pdo->prepare("
                        INSERT INTO invoices (reservation_id, numero_facture, montant_ttc) 
                        VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE montant_ttc = VALUES(montant_ttc)
                    ");
                    $stmt_inv->execute([$pay['res_id'], $numero_facture, $pay['montant']]);

                    // 3. Envoyer l'e-mail de confirmation au client
                    $to = $pay['email'];
                    $type_texte = ($pay['type_paiement'] === 'acompte_50') ? "votre acompte de 50%" : "la totalité de votre location";
                    $subject = "LocaFiara - Confirmation de votre paiement ($numero_facture)";
                    
                    $body = "Bonjour " . $pay['prenom'] . " " . $pay['nom'] . ",\n\n"
                          . "Nous avons bien vérifié et validé " . $type_texte . " d'un montant de " . number_format($pay['montant'], 0, ',', ' ') . " Ar "
                          . "(Référence : " . $pay['reference_transaction'] . ") pour la location du véhicule " . $pay['marque'] . " " . $pay['modele'] . ".\n\n"
                          . "Votre facture n° " . $numero_facture . " est désormais disponible dans votre espace client.\n"
                          . "Veuillez patienter jusqu'à l'heure prévue de remise des clés à notre agence (le " . date('d/m/Y', strtotime($pay['date_debut'])) . ").\n\n"
                          . "Merci de votre confiance,\nL'équipe LocaFiara.";
                    
                    $headers = "From: no-reply@locafiara.mg\r\nContent-Type: text/plain; charset=UTF-8\r\n";
                    @mail($to, $subject, $body, $headers);

                    $message = "Paiement validé avec succès, facture générée et e-mail envoyé à " . htmlspecialchars($pay['email']) . ".";
                } else {
                    $message = "Le paiement a été marqué comme refusé.";
                }
            }
        } catch (PDOException $e) {
            $message = "Erreur SQL : " . $e->getMessage();
        }
    }
}

// Récupérer tous les paiements (les plus récents / en attente en premier)
$sql_list = "
    SELECT p.*, r.id AS res_id, u.nom, u.prenom, u.email, v.marque, v.modele 
    FROM payments p
    JOIN reservations r ON p.reservation_id = r.id
    JOIN users u ON r.user_id = u.id
    JOIN vehicles v ON r.vehicle_id = v.id
    ORDER BY FIELD(p.statut_paiement, 'en_attente', 'valide', 'refuse'), p.date_paiement DESC
";
$paiements = $pdo->query($sql_list)->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/admin_paiements.css">

<main class="admin-pay-main">
    <div class="admin-pay-header">
        <h2>💳 Gestion et Validation des Paiements Clients</h2>
        <a href="admin_dashboard.php" class="btn-back-admin">← Retour au tableau de bord</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert-banner"><?= $message; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="pay-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Véhicule</th>
                    <th>Opérateur</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Référence Transac.</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paiements)): ?>
                    <tr>
                        <td colspan="8" class="empty-row">Aucun paiement enregistré pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($paiements as $p): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']); ?></strong><br>
                                <small><?= htmlspecialchars($p['email']); ?></small>
                            </td>
                            <td><?= htmlspecialchars($p['marque'] . ' ' . $p['modele']); ?></td>
                            <td><span class="badge-op"><?= strtoupper(str_replace('_', ' ', $p['mode_paiement'])); ?></span></td>
                            <td>
                                <?= ($p['type_paiement'] === 'acompte_50') ? '<span class="badge-type-half">Acompte 50%</span>' : '<span class="badge-type-full">Total 100%</span>'; ?>
                            </td>
                            <td><strong><?= number_format($p['montant'], 0, ',', ' '); ?> Ar</strong></td>
                            <td><code class="ref-code"><?= htmlspecialchars($p['reference_transaction']); ?></code></td>
                            <td>
                                <?php if ($p['statut_paiement'] === 'en_attente'): ?>
                                    <span class="status-badge pending">⏳ En attente</span>
                                <?php elseif ($p['statut_paiement'] === 'valide'): ?>
                                    <span class="status-badge valid">✅ Validé</span>
                                <?php else: ?>
                                    <span class="status-badge refused">❌ Refusé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['statut_paiement'] === 'en_attente'): ?>
                                    <form method="POST" class="action-inline-form">
                                        <input type="hidden" name="payment_id" value="<?= $p['id']; ?>">
                                        <button type="submit" name="action" value="valide" class="btn-validate" onclick="return confirm('Confirmez-vous la réception de ce montant sur votre compte marchand ?');">
                                            Valider
                                        </button>
                                        <button type="submit" name="action" value="refuse" class="btn-reject" onclick="return confirm('Refuser cette référence de paiement ?');">
                                            Refuser
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Traité</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>