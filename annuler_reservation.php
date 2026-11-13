<?php
/**
 * Fichier : annuler_reservation.php
 * Rôle : Annule la réservation si le client refuse les conditions de paiement.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$reservation_id = $_GET['id'] ?? null;

if ($reservation_id) {
    // Vérifier que la réservation appartient bien au client connecté avant d'annuler
    $stmt = $pdo->prepare("UPDATE reservations SET statut_reservation = 'annulee' WHERE id = ? AND user_id = ?");
    $stmt->execute([$reservation_id, $_SESSION['user_id']]);
}

header("Location: mes_reservations.php?info=reservation_annulee");
exit();