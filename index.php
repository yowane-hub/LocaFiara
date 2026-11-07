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
                         LEFT JOIN categories c ON v.category_id = c.id 
                         WHERE v.statut = 'disponible'");
    $vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Grille des véhicules -->
    <div class="vehicles-grid">
        <?php if (empty($vehicules)): ?>
            <p>Aucun véhicule disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($vehicules as $car): ?>
                <div class="car-card">
                    <div class="car-thumb">
                        <!-- Affichage de l'image stockée en binaire dans la base de données (Base64) -->
                        <?php if (!empty($car['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($car['image']); ?>" alt="Photo du véhicule" class="vehicule-img" style="width: 100%; height: 160px; object-fit: cover; border-radius: 6px;">
                        <?php else: ?>
                            <div class="no-image" style="width: 100%; height: 160px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-style: italic; border-radius: 6px;">Pas d'image</div>
                        <?php endif; ?>
                    </div>
                    
                    <span class="car-badge"><?= htmlspecialchars($car['categorie_nom'] ?? 'Général'); ?></span>
                    <h3 class="car-title"><?= htmlspecialchars($car['marque'] . ' ' . $car['modele']); ?></h3>
                    <p class="car-plate">Immatriculation : <?= htmlspecialchars($car['immatriculation']); ?></p>

                    <div class="car-footer">
                        <span class="price-label">Tarif</span>
                        <span class="price-value"><?= number_format($car['prix_par_jour'], 0, ',', ' '); ?> Ar</span>/ jour
                        <a href="reservation.php?id=<?= $car['id']; ?>" class="btn-reserve">
                            Réserver
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php
// 3. Inclusion du pied de page
require_once 'includes/footer.php';
?>