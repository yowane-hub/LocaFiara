<?php
/**
 * Fichier : register.php
 * Rôle : Permettre à un nouvel utilisateur de créer son compte.
 */

session_start();
require_once 'config/db.php';

$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($password)) {
        $message_erreur = "Tous les champs sont obligatoires.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $message_erreur = "Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule et un chiffre.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message_erreur = "Cet email est déjà utilisé.";
        } else {
            // Hachage sécurisé du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertion en base avec le rôle par défaut 'client'
            $sql = "INSERT INTO users (nom, prenom, email, telephone, password, role) VALUES (?, ?, ?, ?, ?, 'client')";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$nom, $prenom, $email, $telephone, $hashed_password])) {
                header("Location: login.php");
                exit();
            } else {
                $message_erreur = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Liaison vers le fichier CSS de style (ou login.css partagé) -->
<link rel="stylesheet" href="assets/css/register.css">
<link rel="stylesheet" href="assets/css/login.css">

<main>
    <div class="auth-container">
        <h1>Créer un compte LocaFiara</h1>

        <?php if (!empty($message_erreur)): ?>
            <div class="alert-error"><?= htmlspecialchars($message_erreur); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required class="form-control" placeholder="Ex: Rakoto">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" required class="form-control" placeholder="Ex: Jean">
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" name="email" id="email" required class="form-control" placeholder="nom@exemple.com">
            </div>

            <div class="form-group">
                <label for="telephone">Numéro de téléphone</label>
                <input type="text" name="telephone" id="telephone" required class="form-control" placeholder="Ex: 034 00 000 00">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                    <button type="button" id="togglePassword" class="eye-btn" aria-label="Afficher ou masquer le mot de passe">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-auth">S'inscrire</button>
        </form>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>

<!-- Même script JS réutilisable pour l'œil -->
<script src="assets/js/login.js" defer></script>