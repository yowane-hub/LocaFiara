<?php
/**
 * Fichier : includes/header.php
 * Rôle : En-tête global du site avec la barre de navigation stylée
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocaFiara - Location de véhicules à Madagascar</title>
    
    <!-- Liaison des feuilles de style globales et du header -->
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>

    <!-- Barre de navigation -->
    <header class="site-header">
        <a href="index.php" class="site-logo">
            🚗 Loca<span>Fiara</span>
        </a>
        
        <nav class="site-nav">
            <a href="index.php">Accueil</a>
            <a href="index.php">Nos Véhicules</a>
            <a href="#">Connexion</a>
            <a href="#" class="btn-inscription">Inscription</a>
        </nav>
    </header>