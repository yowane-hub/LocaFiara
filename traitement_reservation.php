<?php
/**
 * Fichier : traitement_reservation.php
 * Rôle : Traitement sécurisé de la réservation, calcul dynamique du montant total 
 *        (tarif journalier du véhicule + option chauffeur) et enregistrement en base de données.
 */

// Démarrage de la session pour identifier l'utilisateur connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Sécurité : Vérification si l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    // Si non connecté, redirection vers la page de connexion
    header("Location: login.php");
    exit();
}

// Inclusion du fichier de connexion à la base de données (PDO)
require_once 'config/db.php';

// 2. Vérification de la méthode de la requête HTTP (doit être POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération des données envoyées par le formulaire de réservation
    $user_id = $_SESSION['user_id'];
    $vehicle_id = $_POST['vehicle_id'] ?? null;
    $date_debut = $_POST['date_debut'] ?? null;
    $date_fin = $_POST['date_fin'] ?? null;
    $avec_chauffeur = isset($_POST['avec_chauffeur']) ? 1 : 0; // 1 si coché, 0 sinon

    // Attribution automatique des villes par défaut (Antsirabe) en arrière-plan
    $ville_depart = "Antsirabe";
    $ville_restitution = "Antsirabe";

    // Validation de base : s'assurer que les champs obligatoires ne sont pas vides
    if (empty($vehicle_id) || empty($date_debut) || empty($date_fin)) {
        die("Erreur : Tous les champs obligatoires (véhicule et dates) doivent être remplis.");
    }

    // 3. Récupération du tarif journalier exact du véhicule depuis la base de données
    $stmt = $pdo->prepare("SELECT prix_par_jour FROM vehicles WHERE id = ?");
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        die("Erreur : Véhicule introuvable.");
    }

    $prix_par_jour = (float)$vehicle['prix_par_jour'];

    // 4. Calcul de la durée du séjour en jours
    $debut = new DateTime($date_debut);
    $fin = new DateTime($date_fin);
    
    // Vérification chronologique des dates
    if ($fin < $debut) {
        die("Erreur : La date de fin doit être postérieure ou égale à la date de début.");
    }

    // Calcul de l'intervalle entre les deux dates (+1 pour inclure le premier jour de location)
    $interval = $debut->diff($fin);
    $jours = $interval->days + 1;

    // 5. Calcul du montant total de la location
    // (Nombre de jours × Tarif journalier du véhicule)
    $montant_total = $jours * $prix_par_jour;

    // Si l'option chauffeur est cochée, on ajoute 20 000 Ar par jour
    if ($avec_chauffeur) {
        $montant_total += ($jours * 20000);
    }

    // Initialisation des autres champs financiers et du statut par défaut
    $montant_acompte = 0;
    $montant_solde = $montant_total;
    $montant_caution = 0;
    $statut_reservation = "en_attente";

    // 6. Insertion sécurisée de la réservation dans la table 'reservations'
    $sql = "INSERT INTO reservations (user_id, vehicle_id, ville_depart, ville_restitution, date_debut, date_fin, montant_total, montant_acompte, montant_solde, montant_caution, statut_reservation, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $user_id, 
        $vehicle_id, 
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

    // 7. Redirection vers l'historique des réservations de l'utilisateur en cas de succès
    header("Location: mes_reservations.php");
    exit();

} else {
    // Si l'accès au fichier se fait autrement que par POST, redirection vers l'accueil
    header("Location: index.php");
    exit();
}