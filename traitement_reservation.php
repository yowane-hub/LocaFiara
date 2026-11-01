<?php
/**
 * Fichier : traitement_reservation.php
 * Rôle : Traiter le formulaire de réservation et l'enregistrer pour l'utilisateur connecté.
 */

// 1. Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion de la configuration de la base de données
require_once 'config/db.php';

// 2. Sécurité : Vérifier si l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    // S'il n'est pas connecté, on le redirige vers la page de connexion
    header("Location: login.php");
    exit();
}

// Récupération de l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// 3. Vérification si le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération des données du formulaire
    $vehicle_id         = $_POST['vehicle_id'] ?? null;
    $date_debut         = trim($_POST['date_debut'] ?? '');
    $date_fin           = trim($_POST['date_fin'] ?? '');
    $ville_depart       = trim($_POST['ville_depart'] ?? '');
    $ville_restitution  = trim($_POST['ville_restitution'] ?? '');
    $driver_id          = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null;

    // 4. Validation basique des champs obligatoires
    if (empty($vehicle_id) || empty($date_debut) || empty($date_fin) || empty($ville_depart) || empty($ville_restitution)) {
        die("Erreur : Tous les champs obligatoires doivent être remplis.");
    }

    // 5. Calcul des montants et de la durée (côté serveur pour des raisons de sécurité)
    // Récupération du prix journalier du véhicule
    $sql_vehicule = "SELECT prix_journalier FROM vehicles WHERE id = ?";
    $stmt_vehicule = $pdo->prepare($sql_vehicule);
    $stmt_vehicule->execute([$vehicle_id]);
    $vehicule = $stmt_vehicule->fetch();

    if (!$vehicule) {
        die("Erreur : Véhicule introuvable.");
    }

    $prix_journalier = $vehicule['prix_journalier'];

    // Calcul du nombre de jours
    $timestamp_debut = strtotime($date_debut);
    $timestamp_fin = strtotime($date_fin);
    
    if ($timestamp_fin <= $timestamp_debut) {
        die("Erreur : La date de fin doit être postérieure à la date de début.");
    }

    $nb_jours = ceil(($timestamp_fin - $timestamp_debut) / (60 * 60 * 24));
    
    // Calcul du montant total (exemple de base + option chauffeur si sélectionné)
    $frais_chauffeur = $driver_id ? (20000 * $nb_jours) : 0;
    $montant_total = ($prix_journalier * $nb_jours) + $frais_chauffeur;
    
    // Calcul de l'acompte (30%)
    $montant_acompte = $montant_total * 0.30;
    $montant_solde = $montant_total - $montant_acompte;
    $montant_caution = 100000; // Caution fixe par exemple
    $statut_reservation = 'en_attente';

    // 6. Insertion sécurisée dans la base de données
    $sql_insert = "INSERT INTO reservations (
                    user_id, vehicle_id, driver_id, ville_depart, ville_restitution, 
                    date_debut, date_fin, montant_total, montant_acompte, 
                    montant_solde, montant_caution, statut_reservation, created_at
                   ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt_insert = $pdo->prepare($sql_insert);
    
    $resultat = $stmt_insert->execute([
        $user_id,
        $vehicle_id,
        $driver_id,
        $ville_depart,
        $ville_restitution,
        $date_debut,
        $date_fin,
        $montant_total,
        $montant_acompte,
        $montant_solde,
        $montant_caution,
        $statut_reservation
    ]);

    if ($resultat) {
        // Redirection vers la page des réservations de l'utilisateur
        header("Location: mes_reservations.php");
        exit();
    } else {
        echo "Une erreur est survenue lors de l'enregistrement de la réservation.";
    }
} else {
    // Si on accède au fichier directement sans soumettre le formulaire
    header("Location: index.php");
    exit();
}