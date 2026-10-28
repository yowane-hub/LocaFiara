<?php
/**
 * Fichier : includes/header.php
 * Rôle : En-tête global du site (HTML head, styles Tailwind et barre de navigation)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocaFiara - Location de Voitures à Madagascar</title>
    <!-- Chargement du fichier CSS Tailwind compilé en local -->
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>
<body class="bg-gray-100 text-gray-800 flex flex-col min-h-screen">

    <!-- Barre de navigation principale -->
    <header class="bg-blue-900 text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo du site -->
            <a href="index.php" class="text-2xl font-bold tracking-wide flex items-center gap-2">
                🚗 Loca<span class="text-yellow-400">Fiara</span>
            </a>
            <!-- Menu de navigation -->
            <nav class="space-x-6 font-medium">
                <a href="index.php" class="hover:text-yellow-400 transition">Accueil</a>
                <a href="#" class="hover:text-yellow-400 transition">Nos Véhicules</a>
                <a href="#" class="hover:text-yellow-400 transition">Connexion</a>
                <a href="#" class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 font-semibold px-4 py-2 rounded-lg transition">Inscription</a>
            </nav>
        </div>
    </header>

    <!-- Conteneur principal dynamique -->
    <main class="container mx-auto px-4 py-8 flex-grow">