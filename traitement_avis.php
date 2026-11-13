<?php
/**
 * Fichier : traitement_avis.php
 * Rôle : Traite et enregistre l'avis et la note laissés par un client pour une réservation.
 */

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Inclusion de la connexion à la base de données
require_once 'config/db.php';

// Vérification si les données ont bien été envoyées via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $reservation_id = $_POST['reservation_id'] ?? null;
    $note = intval($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    // Validation des données reçues
    if ($reservation_id && $note >= 1 && $note <= 5 && !empty($commentaire)) {
        try {
            // Insertion de l'avis dans la table 'reviews' en respectant ta structure de base de données
            $sql = "INSERT INTO reviews (reservation_id, user_id, note, commentaire, date_avis) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$reservation_id, $user_id, $note, $commentaire]);

            // Redirection vers la page des réservations avec un succès
            header("Location: mes_reservations.php?success=avis_enregistre");
            exit();

        } catch (PDOException $e) {
            die("Erreur lors de l'enregistrement de l'avis : " . $e->getMessage());
        }
    } else {
        die("Erreur : Tous les champs du formulaire d'avis doivent être remplis correctement.");
    }
} else {
    // Si on accède au fichier directement via l'URL sans soumettre le formulaire
    header("Location: mes_reservations.php");
    exit();
}