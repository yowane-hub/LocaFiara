<?php
/**
 * Fichier : traitement_paiement.php
 * Rôle : Enregistre la demande de paiement du client avec le statut 'en_attente' de validation admin.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'] ?? null;
    $mode_paiement = $_POST['mode_paiement'] ?? null;
    $type_paiement = $_POST['type_paiement'] ?? null;
    $reference_transaction = trim($_POST['reference_transaction'] ?? '');
    $montant = $_POST['montant'] ?? 0;

    if ($reservation_id && $mode_paiement && $type_paiement && !empty($reference_transaction) && $montant > 0) {
        try {
            // Enregistrement du paiement avec le statut 'en_attente'
            $sql = "INSERT INTO payments (reservation_id, mode_paiement, type_paiement, reference_transaction, montant, statut_paiement, date_paiement) 
                    VALUES (?, ?, ?, ?, ?, 'en_attente', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$reservation_id, $mode_paiement, $type_paiement, $reference_transaction, $montant]);

            // Redirection vers mes réservations avec un message de succès (en attente de validation)
            header("Location: mes_reservations.php?success=paiement_soumis");
            exit();

        } catch (PDOException $e) {
            die("Erreur lors de l'enregistrement du paiement : " . $e->getMessage());
        }
    } else {
        die("Erreur : Tous les champs du formulaire de paiement doivent être remplis.");
    }
} else {
    header("Location: mes_reservations.php");
    exit();
}