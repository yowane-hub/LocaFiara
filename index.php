<?php
/**
 * Fichier : index.php
 * Rôle : Page d'accueil / Catalogue des véhicules disponibles
 */

// 1. Connexion à la base de données
require_once 'config/db.php';

// 2. Inclusion de l'en-tête du site
require_once 'includes/header.php';
?>

<!-- Liaison du CSS spécifique à l'accueil -->
<link rel="stylesheet" href="assets/css/index.css">

<main>
    <!-- En-tête de la page -->
    <div class="home-header">
        <h1>Louez votre véhicule à Madagascar</h1>
        <p>Des 4x4 et voitures de qualité pour tous vos déplacements en toute sérénité.</p>
    </div>

    <h2 style="font-size: 22px; font-weight: 700; color: #1e3a8a; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;">
        Véhicules Disponibles
    </h2>

    <?php
    // Récupération des véhicules disponibles dans la base de données
    $stmt = $pdo->query("SELECT v.*, c.nom AS categorie_nom 
                         FROM vehicles v 
                         JOIN categories c ON v.category_id = c.id 
                         WHERE v.statut = 'disponible'");
    $vehicles = $stmt->fetchAll();
    ?>

    <!-- Grille des véhicules -->
    <div class="vehicles-grid">
        <?php foreach ($vehicles as $car): ?>
            <div class="car-card">
                <div>
                    <div class="car-thumb">
                        🚗 <?= htmlspecialchars($car['marque'] . ' ' . $car['modele']); ?>
                    </div>
                    
                    <span class="car-badge"><?= htmlspecialchars($car['categorie_nom']); ?></span>
                    <h3 class="car-title"><?= htmlspecialchars($car['marque'] . ' ' . $car['modele']); ?></h3>
                    <p class="car-plate">Immatriculation : <?= htmlspecialchars($car['immatriculation']); ?></p>
                </div>
                
                <div class="car-footer">
                    <div>
                        <span class="price-label">Tarif</span>
                        <span class="price-value"><?= number_format($car['prix_par_jour'], 0, ',', ' '); ?> Ar</span> <span style="font-size: 13px; color: #6b7280;">/ jour</span>
                    </div>
                    <a href="reservation.php?id=<?= $car['id']; ?>" class="btn-reserve">
                        Réserver
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php
// 3. Inclusion du pied de page
require_once 'includes/footer.php';
?>