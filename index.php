<?php
/**
 * Fichier : index.php
 * Rôle : Page d'accueil et catalogue dynamique des véhicules disponibles à la location
 */

// Appel du fichier de configuration et de connexion à la base de données
require_once 'config/db.php';

// Requête SQL sécurisée avec une jointure (JOIN) pour récupérer le nom de la catégorie de chaque véhicule
$sql = "SELECT v.*, c.nom AS categorie_nom 
        FROM vehicles v 
        JOIN categories c ON v.category_id = c.id 
        WHERE v.statut = 'disponible'";

$stmt = $pdo->query($sql);
$vehicles =$stmt->fetchAll();

// Inclusion de l'en-tête HTML
require_once 'includes/header.php';
?>

<!-- Section d'en-tête / Hero de la page d'accueil -->
<section class="mb-10 text-center">
    <h1 class="text-4xl font-extrabold text-blue-900 mb-2">Louez votre véhicule à Madagascar</h1>
    <p class="text-gray-600 text-lg">Des 4x4 et voitures de qualité pour tous vos déplacements en toute sérénité.</p>
</section>

<!-- Titre de la section catalogue -->
<h2 class="text-2xl font-bold text-gray-800 mb-6 border-b-2 border-yellow-400 pb-2 inline-block">Véhicules Disponibles</h2>

<!-- Grille d'affichage des véhicules (Responsive avec Tailwind) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($vehicles as$car): ?>
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition">
            
            <!-- Zone d'illustration visuelle du véhicule -->
            <div class="bg-gray-200 h-48 flex items-center justify-center text-gray-500 font-bold text-xl">
                🚗 <?= htmlspecialchars($car['marque'] . ' ' .$car['modele']); ?>
            </div>
            
            <div class="p-5">
                <!-- Badge de la catégorie -->
                <span class="text-xs font-semibold uppercase px-2.5 py-1 rounded bg-blue-100 text-blue-800 mb-2 inline-block">
                    <?= htmlspecialchars($car['categorie_nom']); ?>
                </span>
                
                <!-- Nom et modèle du véhicule -->
                <h3 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($car['marque'] . ' ' .$car['modele']); ?></h3>
                
                <!-- Numéro d'immatriculation protégé par htmlspecialchars (sécurité XSS) -->
                <p class="text-sm text-gray-500 mb-4">Immatriculation : <?= htmlspecialchars($car['immatriculation']); ?></p>
                
                <!-- Bloc prix et bouton de réservation -->
                <div class="flex items-center justify-between border-t pt-4">
                    <div>
                        <span class="text-2xl font-extrabold text-blue-900"><?= number_format($car['prix_par_jour'], 0, ',', ' '); ?></span>
                        <span class="text-sm text-gray-500"> Ar / jour</span>
                    </div>
                    <a href="#" class="bg-blue-900 hover:bg-blue-800 text-white font-medium px-4 py-2 rounded-lg text-sm transition">
                        Réserver
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
// Inclusion du pied de page HTML
require_once 'includes/footer.php';
?>