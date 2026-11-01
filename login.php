<?php
/**
 * Fichier : login.php
 * Rôle : Permettre à un utilisateur de se connecter à son compte.
 */

// 1. Démarrage de la session (obligatoire pour gérer les connexions)
session_start();

// 2. Inclusion de la configuration de la base de données
require_once 'config/db.php';

$message_erreur = "";

// 3. Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email    = trim($_POST['email'] ?? '');
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
            // Lors de la connexion réussie dans login.php :
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom']; // <-- Indispensable pour récupérer le prénom !
            $_SESSION['user_email'] = $user['email'];

            // Redirection vers l'accueil ou la page des réservations
            header("Location: index.php");
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
                <input type="password" name="password" id="password" required class="form-control" placeholder="••••••••">
            </div>

            <button type="submit" class="btn-auth">Se connecter</button>
        </form>
    </div>
</main>

<?php
// 5. Inclusion du pied de page
require_once 'includes/footer.php';
?>