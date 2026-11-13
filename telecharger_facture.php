<?php
/**
 * Fichier : telecharger_facture.php
 * Rôle : Génère la facture officielle pour téléchargement / impression PDF par le client.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$invoice_id = $_GET['id'] ?? null;

if (!$invoice_id) {
    die("Facture introuvable.");
}

// Récupérer les informations de la facture, de la réservation, du véhicule et du client
$stmt = $pdo->prepare("
    SELECT i.*, r.date_debut, r.date_fin, r.montant_total, 
           v.marque, v.modele, v.immatriculation,
           u.nom, u.prenom, u.email, u.telephone
    FROM invoices i
    JOIN reservations r ON i.reservation_id = r.id
    JOIN vehicles v ON r.vehicle_id = v.id
    JOIN users u ON r.user_id = u.id
    WHERE i.id = ? AND r.user_id = ?
");
$stmt->execute([$invoice_id, $_SESSION['user_id']]);
$facture = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$facture) {
    die("Accès refusé ou facture inexistante.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= htmlspecialchars($facture['numero_facture']); ?></title>
    <link rel="stylesheet" href="assets/css/facture.css">
</head>
<body>

    <div class="invoice-container">
        <!-- En-tête de la facture -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>LocaFiara</h1>
                <p>Agence de Location de Véhicules</p>
                <p>Antananarivo, Madagascar</p>
                <p>contact@locafiara.mg | +261 34 00 000 00</p>
            </div>
            <div class="invoice-meta">
                <h2>FACTURE</h2>
                <p><strong>N° :</strong> <?= htmlspecialchars($facture['numero_facture']); ?></p>
                <p><strong>Date d'émission :</strong> <?= date('d/m/Y', strtotime($facture['date_emission'])); ?></p>
            </div>
        </div>

        <hr class="divider">

        <!-- Informations Client -->
        <div class="client-section">
            <h3>Facturé à :</h3>
            <p><strong><?= htmlspecialchars($facture['prenom'] . ' ' . $facture['nom']); ?></strong></p>
            <p>Téléphone : <?= htmlspecialchars($facture['telephone']); ?></p>
            <p>E-mail : <?= htmlspecialchars($facture['email']); ?></p>
        </div>

        <!-- Tableau des détails -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description de la prestation</th>
                    <th>Période de location</th>
                    <th>Montant TTC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Location de véhicule</strong><br>
                        Modèle : <?= htmlspecialchars($facture['marque'] . ' ' . $facture['modele']); ?><br>
                        Immatriculation : <?= htmlspecialchars($facture['immatriculation']); ?>
                    </td>
                    <td>
                        Du <?= date('d/m/Y', strtotime($facture['date_debut'])); ?><br>
                        au <?= date('d/m/Y', strtotime($facture['date_fin'])); ?>
                    </td>
                    <td><strong><?= number_format($facture['montant_ttc'], 0, ',', ' '); ?> Ar</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Total -->
        <div class="invoice-total">
            <p><span>Total à payer :</span> <strong><?= number_format($facture['montant_ttc'], 0, ',', ' '); ?> Ar</strong></p>
            <p class="status-paid">Statut : PAYÉ ET VALIDÉ</p>
        </div>

        <!-- Pied de page -->
        <div class="invoice-footer">
            <p>Merci pour votre confiance. Bon voyage avec LocaFiara !</p>
            <p><small>Cette facture fait office de reçu officiel de paiement.</small></p>
        </div>

        <!-- Bouton d'action impression / PDF -->
        <div class="no-print actions-bar">
            <button onclick="window.print();" class="btn-print">🖨️ Télécharger / Imprimer en PDF</button>
            <a href="mes_reservations.php" class="btn-back">← Retour à mes réservations</a>
        </div>
    </div>

</body>
</html>