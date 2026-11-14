<?php
/*
 * Fichier : index.php
 * Rôle : Page d'accueil et catalogue public avec filtrage par catégorie
 */

require_once 'config/db.php';
require_once 'includes/header.php';

// 1. Récupération de la catégorie filtrée via l'URL (ex: index.php?cat=1)
$selected_cat = isset($_GET['cat']) ? intval($_GET['cat']) : 0;

// 2. Récupération de toutes les catégories pour afficher les boutons de filtre
$stmt_categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$all_categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// 3. Construction de la requête SQL de filtrage des véhicules disponibles
if ($selected_cat > 0) {
    $stmt_vehicles = $pdo->prepare("SELECT v.*, c.nom AS categorie_nom 
                                     FROM vehicles v 
                                     LEFT JOIN categories c ON v.category_id = c.id 
                                     WHERE v.statut = 'disponible' AND v.category_id = :cat_id
                                     ORDER BY v.id DESC");
    $stmt_vehicles->execute([':cat_id' => $selected_cat]);
} else {
    $stmt_vehicles = $pdo->query("SELECT v.*, c.nom AS categorie_nom 
                                  FROM vehicles v 
                                  LEFT JOIN categories c ON v.category_id = c.id 
                                  WHERE v.statut = 'disponible'
                                  ORDER BY v.id DESC");
}
$vehicules = $stmt_vehicles->fetchAll(PDO::FETCH_ASSOC);
?>
 
<link rel="stylesheet" href="assets/css/vehicules.css">

<main class="home-main">
    <!-- En-tête de la page -->
    <div class="home-header">
        <h1>Louez votre véhicule à Madagascar</h1>
        <p>Choisissez parmi notre flotte de voitures, motos et camions en toute sérénité.</p>
    </div>

    <!-- Barre de filtrage par catégorie -->
    <div class="filter-container">
        <a href="vehicules.php" class="filter-btn <?= ($selected_cat === 0) ? 'active' : ''; ?>">
            Tous
        </a>
        <?php foreach ($all_categories as $cat): ?>
            <a href="vehicules.php?cat=<?= $cat['id']; ?>" class="filter-btn <?= ($selected_cat === $cat['id']) ? 'active' : ''; ?>">
                <?= htmlspecialchars($cat['nom']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Grille des véhicules disponibles -->
    <div class="vehicles-grid">
        <?php if (empty($vehicules)): ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #6b7280; padding: 40px 0;">
                Aucun véhicule disponible dans cette catégorie pour le moment.
            </p>
        <?php else: ?>
            <?php foreach ($vehicules as $car): ?>
                <div class="car-card">
                    <!-- Vignette image propre -->
                    <div class="car-thumb">
                        <?php if (!empty($car['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($car['image']); ?>" alt="Photo du véhicule">
                        <?php else: ?>
                            <div class="no-image">Pas d'image</div>
                        <?php endif; ?>
                    </div>
                    
                    <span class="car-badge"><?= htmlspecialchars($car['categorie_nom'] ?? 'Général'); ?></span>
                    
                    <h3 class="car-title"><?= htmlspecialchars($car['marque'] . ' ' . $car['modele']); ?></h3>
                    <p class="car-plate">Immatriculation : <strong><?= htmlspecialchars($car['immatriculation']); ?></strong></p>

                    <div class="car-footer">
                        <div>
                            <span class="price-value"><?= number_format($car['prix_par_jour'], 0, ',', ' '); ?> Ar</span> / jour
                        </div>
                        <a href="reservation.php?id=<?= $car['id']; ?>" class="btn-reserve">
                            Réserver
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>