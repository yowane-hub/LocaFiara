<?php
/**
 * Fichier : login.php
 * Rôle : Permettre à un utilisateur de se connecter à son compte.
 */

// 1. Démarrage de la session
session_start();

// 2. Inclusion de la configuration de la base de données
require_once 'config/db.php';

$message_erreur = "";

// 3. Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message_erreur = "Tous les champs sont obligatoires.";
    } else {
        // Recherche de l'utilisateur dans la base de données
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Vérification de l'existence de l'utilisateur et du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            
            // Création des variables de session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            
            // Stockage du rôle dans la session
            $_SESSION['role'] = $user['role'];

            // Redirection intelligente selon le rôle de l'utilisateur
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            $message_erreur = "Email ou mot de passe incorrect.";
        }
    }
}

// 4. Inclusion de l'en-tête du site
require_once 'includes/header.php';
?>

<!-- Liaison vers le fichier CSS externe -->
<link rel="stylesheet" href="assets/css/login.css">

<main>
    <div class="auth-container">
        <h1>Connexion à LocaFiara</h1>

        <!-- Affichage des messages d'erreur -->
        <?php if (!empty($message_erreur)): ?>
            <div class="alert-error"><?= htmlspecialchars($message_erreur); ?></div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" name="email" id="email" required class="form-control" placeholder="nom@exemple.com">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                    <button type="button" id="togglePassword" class="eye-btn" aria-label="Afficher ou masquer le mot de passe">
                        <!-- Icône œil normal par défaut (SVG) -->
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-auth">Se connecter</button>
        </form>
    </div>
</main>

<!-- 5. Inclusion du pied de page -->
<?php
require_once 'includes/footer.php';
?>

<!-- Script JS pour afficher/masquer le mot de passe -->
<script src="assets/js/login.js" defer></script>