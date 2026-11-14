<?php
/**
 * Fichier : index.php
 * Rôle : Page d'accueil moderne avec texte à gauche et voiture dynamique 360° à droite.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/index.css">

<main class="home-main">
    <section class="hero-section">
        <!-- Partie Gauche : Texte publicitaire et CTA -->
        <div class="hero-content">
            <span class="badge-promo">🚀 Expérience Premium à Madagascar</span>
            <h1>Louez le véhicule de vos rêves avec <span>LocaFiara</span></h1>
            <p>
                Découvrez notre flotte exclusive de voitures citadines, SUV, motos et camions. 
                Profitez d'une réservation simple, rapide et sécurisée par Mobile Money pour tous vos déplacements à Antsirabe et partout sur l'île.
            </p>
            <div class="hero-features">
                <div class="feature-item">✔ Réservation instantanée & sécurisée</div>
                <div class="feature-item">✔ Véhicules récents et entièrement révisés</div>
                <div class="feature-item">✔ Support client disponible 7j/7</div>
            </div>
            <div class="hero-buttons">
                <a href="vehicules.php" class="btn-primary">Découvrir nos véhicules</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn-secondary">Créer un compte</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Partie Droite : Voiture dynamique avec effet visuel 360° -->
        <div class="hero-visual">
            <div class="car-360-wrapper">
                <div class="glow-effect"></div>
                <!-- Image de la voiture de luxe -->
                <img src="assets/image/IMG_5855.WEBP" alt="Voiture dynamique LocaFiara" class="car-dynamic-img">
                <div class="badge-360">
                    <span class="pulse-dot"></span> Vue Dynamique & Prestige
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>