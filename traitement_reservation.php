<?php
/**
 * Fichier : traitement_reservation.php
 * Rôle : Traitement complet de la réservation en accord avec la structure de la BDD
 */

// 1. Inclusion de la connexion à la base de données
require_once 'config/db.php';

// 2. Vérification de la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// 3. Récupération des données du formulaire et des valeurs par défaut/session
// Note : Si tu gères la connexion des utilisateurs, l'ID viendra de la session ($_SESSION['user_id']). 
// Pour l'instant, on met un utilisateur par défaut (ex: ID 1) si la session n'existe pas encore.
$user_id        = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1; 
$vehicle_id     = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
$date_debut     = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
$date_fin       = isset($_POST['date_fin']) ? $_POST['date_fin'] : '';
$avec_chauffeur = isset($_POST['avec_chauffeur']) ? true : false;

// Villes (si tu ne les demandes pas encore dans le formulaire, on met des valeurs par défaut)
$ville_depart     = "Antsirabe"; 
$ville_restitution = "Antsirabe";

// 4. Validations de base
if ($vehicle_id <= 0 || empty($date_debut) || empty($date_fin)) {
    die("Erreur : Informations de réservation incomplètes.");
}

if (strtotime($date_fin) < strtotime($date_debut)) {
    die("Erreur : La date de fin ne peut pas être antérieure à la date de début.");
}

// 5. Récupérer le tarif journalier du véhicule
$sqlVehicle = "SELECT prix_par_jour FROM vehicles WHERE id = ? AND statut = 'disponible'";
$stmtVehicle = $pdo->prepare($sqlVehicle);
$stmtVehicle->execute([$vehicle_id]);
$vehicle = $stmtVehicle->fetch();

if (!$vehicle) {
    die("Erreur : Ce véhicule n'existe pas ou n'est plus disponible.");
}

$prix_par_jour = (float)$vehicle['prix_par_jour'];

// 6. Calcul de la durée et des montants financiers
$timestamp_debut = strtotime($date_debut);
$timestamp_fin   = strtotime($date_fin);
$nb_jours = (($timestamp_fin - $timestamp_debut) / (60 * 60 * 24)) + 1;

if ($nb_jours <= 0) {
    die("Erreur : La durée de la location doit être d'au moins 1 jour.");
}

// Calcul du montant total
$montant_vehicule = $nb_jours * $prix_par_jour;
$montant_chauffeur = $avec_chauffeur ? ($nb_jours * 20000) : 0;
$montant_total = $montant_vehicule + $montant_chauffeur;

// Gestion des acomptes ou soldes (par exemple, 30% d'acompte demandé à la réservation)
$montant_acompte = $montant_total * 0.30;
$montant_solde   = $montant_total - $montant_acompte;
$montant_caution = 100000; // Caution fixe par exemple

// Gestion du chauffeur (si tu as une table drivers, tu peux attribuer un ID ou NULL)
$driver_id = NULL; 

// 7. Insertion de la réservation dans la table 'reservations' de ta base de données
$sqlInsert = "INSERT INTO reservations (
                user_id, vehicle_id, driver_id, ville_depart, ville_restitution, 
                date_debut, date_fin, montant_total, montant_acompte, 
                montant_solde, montant_caution, statut_reservation
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente_acompte')";

$stmtInsert = $pdo->prepare($sqlInsert);
$succes = $stmtInsert->execute([
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
    $montant_caution
]);

if ($succes) {
    // Affichage d'un message de confirmation propre
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h2 style='color: #1e3a8a;'>🎉 Réservation enregistrée avec succès !</h2>";
    echo "<p>Montant total : <strong>" . number_format($montant_total, 0, ',', ' ') . " Ar</strong> pour " . $nb_jours . " jour(s).</p>";
    echo "<p>Acompte à verser : <strong>" . number_format($montant_acompte, 0, ',', ' ') . " Ar</strong></p>";
    echo "<br><a href='index.php' style='background: #1e3a8a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;'>Retour à l'accueil</a>";
    echo "</div>";
} else {
    echo "Erreur lors de l'enregistrement de la réservation.";
}
?>