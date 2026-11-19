<?php
/**
 * Fichier : payer.php
 * Rôle : Gestion interactive du paiement (Mobile Money 100% ou Espèces avec acompte 50% obligatoire)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$reservation_id = $_GET['reservation_id'] ?? null;

if (!$reservation_id) {
    header("Location: mes_reservations.php");
    exit();
}

// 1. Récupérer les détails de la réservation
$stmt = $pdo->prepare("SELECT r.*, v.marque, v.modele FROM reservations r JOIN vehicles v ON r.vehicle_id = v.id WHERE r.id = ? AND r.user_id = ?");
$stmt->execute([$reservation_id, $_SESSION['user_id']]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("Réservation introuvable ou accès non autorisé.");
}

// 2. Récupérer les numéros marchands de l'entreprise
$stmt_marchands = $pdo->query("SELECT * FROM marchands");
$marchands = $stmt_marchands->fetchAll(PDO::FETCH_ASSOC);

// Calculs des montants
$montant_total = (float)$reservation['montant_total'];
$montant_acompte = $montant_total * 0.5;

require_once 'includes/header.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/css/payer.css">

<main class="payment-main">
    <div class="payment-card">
        <h2>Règlement de votre location</h2>
        <p class="payment-vehicle">Véhicule : <strong><?= htmlspecialchars($reservation['marque'] . ' ' . $reservation['modele']); ?></strong></p>
        
        <div class="summary-box">
            <div class="summary-item">
                <span>Montant total de la location :</span>
                <strong><?= number_format($montant_total, 0, ',', ' '); ?> Ar</strong>
            </div>
            <div class="summary-item highlight">
                <span>Acompte requis si paiement en espèces (50%) :</span>
                <strong><?= number_format($montant_acompte, 0, ',', ' '); ?> Ar</strong>
            </div>
        </div>

        <!-- Étape 1 : Choix de la méthode globale -->
        <div class="method-selector">
            <label class="section-label">1. Comment souhaitez-vous régler votre réservation ?</label>
            <div class="method-buttons">
                <button type="button" id="btn-choice-mobile" class="method-btn active">
                    📱 Mobile Money (100%)
                </button>
                <button type="button" id="btn-choice-espece" class="method-btn">
                    💵 Paiement en Espèces
                </button>
            </div>
        </div>

        <!-- Encart d'avertissement pour l'option Espèces (masqué par défaut) -->
        <div id="espece-warning-box" class="warning-box hidden">
            <h4>⚠️ Condition pour le paiement en espèces</h4>
            <p>Pour garantir la réservation du véhicule, un <strong>acompte obligatoire de 50% (<?= number_format($montant_acompte, 0, ',', ' '); ?> Ar)</strong> doit être versé immédiatement via Mobile Money. Les 50% restants seront payés en espèces lors de la remise des clés à l'agence.</p>
            <div class="warning-actions">
                <button type="button" id="btn-accept-acompte" class="btn-accept">Accepter et payer l'acompte (50%)</button>
                <a href="annuler_reservation.php?id=<?= $reservation['id']; ?>" class="btn-refuse" onclick="return confirm('Êtes-vous sûr de vouloir refuser et annuler cette réservation ?');">Refuser (Annuler la réservation)</a>
            </div>
        </div>

        <!-- Formulaire de transfert Mobile Money (visible par défaut pour 100%, ou après acceptation pour 50%) -->
        <div id="mobile-payment-section">
            <div class="merchants-list">
                <h4>Comptes Marchands LocaFiara :</h4>
                <p class="merchant-note">Veuillez effectuer votre transfert vers l'un des numéros officiels ci-dessous avant de soumettre votre référence :</p>
                <div class="merchants-grid">
                    <?php foreach ($marchands as $m): ?>
                        <div class="merchant-card">
                            <span class="merchant-op"><?= htmlspecialchars($m['operateur']); ?></span>
                            <strong class="merchant-num"><?= htmlspecialchars($m['numero']); ?></strong>
                            <span class="merchant-owner">Titulaire : <?= htmlspecialchars($m['titulaire']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <form action="traitement_paiement.php" method="POST" class="payment-form">
                <input type="hidden" name="reservation_id" value="<?= $reservation['id']; ?>">
                <input type="hidden" name="type_paiement" id="input_type_paiement" value="total">
                <input type="hidden" name="montant" id="input_montant" value="<?= $montant_total; ?>">

                <div class="amount-to-pay-banner">
                    Montant à transférer maintenant : <strong id="display_montant"><?= number_format($montant_total, 0, ',', ' '); ?> Ar</strong>
                </div>

                <div class="form-group">
                    <label for="mode_paiement">Opérateur utilisé pour le transfert :</label>
                    <select name="mode_paiement" id="mode_paiement" required class="form-control">
                        <option value="mvola">MVola (Telma)</option>
                        <option value="orange_money">Orange Money</option>
                        <option value="airtel_money">Airtel Money</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reference_transaction">Référence de la transaction (Reçu SMS) :</label>
                    <input type="text" name="reference_transaction" id="reference_transaction" placeholder="Ex: MV261113.1420.A98765" required class="form-control">
                </div>

                <button type="submit" class="btn-pay">
                    Envoyer la référence pour validation
                </button>
            </form>
        </div>

        <div class="back-link-container">
            <a href="mes_reservations.php" class="back-link">← Retour à mes réservations</a>
        </div>
    </div>
</main>

<script>
    const btnMobile = document.getElementById('btn-choice-mobile');
    const btnEspece = document.getElementById('btn-choice-espece');
    const warningBox = document.getElementById('espece-warning-box');
    const mobileSection = document.getElementById('mobile-payment-section');
    const btnAcceptAcompte = document.getElementById('btn-accept-acompte');

    const inputType = document.getElementById('input_type_paiement');
    const inputMontant = document.getElementById('input_montant');
    const displayMontant = document.getElementById('display_montant');

    const montantTotal = <?= $montant_total; ?>;
    const montantAcompte = <?= $montant_acompte; ?>;
    const formatTotal = "<?= number_format($montant_total, 0, ',', ' '); ?> Ar";
    const formatAcompte = "<?= number_format($montant_acompte, 0, ',', ' '); ?> Ar";

    // Choix : Mobile Money 100%
    btnMobile.addEventListener('click', () => {
        btnMobile.classList.add('active');
        btnEspece.classList.remove('active');
        warningBox.classList.add('hidden');
        mobileSection.classList.remove('hidden');

        inputType.value = 'total';
        inputMontant.value = montantTotal;
        displayMontant.textContent = formatTotal;
    });

    // Choix : Espèces (affiche la condition des 50%)
    btnEspece.addEventListener('click', () => {
        btnEspece.classList.add('active');
        btnMobile.classList.remove('active');
        warningBox.classList.remove('hidden');
        mobileSection.classList.add('hidden');
    });

    // Acceptation des 50% d'acompte
    btnAcceptAcompte.addEventListener('click', () => {
        warningBox.classList.add('hidden');
        mobileSection.classList.remove('hidden');

        inputType.value = 'acompte_50';
        inputMontant.value = montantAcompte;
        displayMontant.textContent = formatAcompte + " (Acompte 50%)";
    });
</script>

<?php require_once 'includes/footer.php'; ?>