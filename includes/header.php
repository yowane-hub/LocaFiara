<?php
/**
 * Fichier : includes/header.php
 * Rôle : En-tête global du site avec la barre de navigation stylée
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocaFiara - Location de véhicules à Madagascar</title>
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>

<header class="site-header">
    <a href="index.php" class="site-logo">
         🚗 Loca<span>Fiara</span>
    </a>

    <nav class="site-nav">
        <a href="index.php">Accueil</a>
        <a href="index.php">Nos Véhicules</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Liens si l'utilisateur est connecté -->
            <a href="mes_reservations.php">Mes Réservations</a>
            <span class="user-greeting">
                Bonjour, <?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Utilisateur'); ?>
            </span>
            <!-- Bouton Déconnexion avec style direct (fond rouge, même style que l'inscription) -->
            <a href="logout.php" style="background-color: #ef4444; color: #ffffff; padding: 10px 20px; border-radius: 12px; font-weight: 700; text-decoration: none;">Déconnexion</a>
        <?php else: ?>
            <!-- Liens si l'utilisateur est un visiteur -->
            <a href="login.php">Connexion</a>
            <a href="register.php" class="btn-inscription">Inscription</a>
        <?php endif; ?>
    </nav>
</header>