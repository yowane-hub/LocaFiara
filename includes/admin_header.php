<?php
/*
 * Fichier : includes/admin_header.php
 * Rôle : En-tête de navigation exclusif à l'espace administrateur
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
    <title>Administration - LocaFiara</title>
    <!-- Feuilles de style globales et spécifiques admin -->
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/menu.css?v=2">
</head>
<body>
    <header>
        <div class="admin-header-container">
            
            <!-- Logo et identification de l'espace -->
            <div class="admin-logo">
                <a href="admin_dashboard.php">
                    🚗 Loca <span style="color: #facc15;">Fiara</span> 
                    <span class="badge-admin">Admin</span>
                </a>   
            </div>
 
            <!-- Menu de navigation de gestion -->
            <button type="button" id="menu-toggle" class="menu-toggle-btn" aria-label="Ouvrir le menu" aria-controls="main-nav" aria-expanded="false">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <circle cx="5" cy="12" r="2"></circle>
            <circle cx="12" cy="12" r="2"></circle>
            <circle cx="19" cy="12" r="2"></circle>
        </svg>
    </button>
 
    <nav class="admin-nav-links" id="main-nav">
                <a href="admin_dashboard.php">Tableau de bord</a>
                <a href="admin_vehicule.php">Gérer les Véhicules</a>
                
                <!-- Lien vers le site public -->
                <a href="index.php" target="_blank" class="admin-link-site">Voir le site ↗</a>
                
                <!-- Bouton de déconnexion -->
                <a href="logout.php" class="admin-btn-logout" style="background-color: #ef4444; color: #ffffff; padding: 10px 20px; border-radius: 12px; font-weight: 700; text-decoration: none;">Déconnexion</a>
                <!-- Bouton Dark Mode -->
                <button id="theme-toggle" class="theme-toggle-btn" title="Changer de thème">
                    🌙
                </button>
            </nav>
 
        </div>
        <script src="assets/js/theme.js"></script>
        <script src="assets/js/menu.js"></script>
 
    </header>