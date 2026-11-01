<?php
/**
 * Fichier : register.php
 * Rôle : Permettre à un nouvel utilisateur de s'inscrire sur LocaFiara.
 */

// 1. Inclusion de la configuration de la base de données
require_once 'config/db.php';

$message_erreur = "";
$message_succes = "";

// 2. Vérification si le formulaire a été soumis via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération des champs du formulaire
    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = $_POST['password'] ?? '';

    //  Vérifier si les champs ne sont pas vides
    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($password)) {
        $message_erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Vérification de la validité du format de l'email
        $message_erreur = "L'adresse email n'est pas valide.";
    } else {
        
        //  Vérifier si l'email existe déjà dans la base de données
        $sql_verif = "SELECT id FROM users WHERE email = ?";
        $stmt_verif = $pdo->prepare($sql_verif);
        $stmt_verif->execute([$email]);
        
        if ($stmt_verif->rowCount() > 0) {
            $message_erreur = "Cette adresse email est déjà utilisée par un autre compte.";
        } else {
            
            //  Stockage sécurisé du mot de passe
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            //  Enregistrement dans la table `users` avec le téléphone
            $sql_insert = "INSERT INTO users (nom, prenom, email, telephone, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt_insert = $pdo->prepare($sql_insert);
            $resultat = $stmt_insert->execute([$nom, $prenom, $email, $telephone, $password_hash]);

            if ($resultat) {
                $message_succes = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $message_erreur = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}

// 3. Inclusion de l'en-tête du site
require_once 'includes/header.php';
?>

<!-- Liaison propre vers le fichier CSS externe -->
<link rel="stylesheet" href="assets/css/register.css">

<main>
    <div class="auth-container">
        <h1>Créer un compte LocaFiara</h1>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php if (!empty($message_erreur)): ?>
            <div class="alert-error"><?= htmlspecialchars($message_erreur); ?></div>
        <?php endif; ?>

        <?php if (!empty($message_succes)): ?>
            <div class="alert-success"><?= htmlspecialchars($message_succes); ?></div>
        <?php endif; ?>

        <!-- Formulaire HTML d'inscription mis à jour avec le champ Téléphone -->
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
                <input type="password" name="password" id="password" required class="form-control" placeholder="••••••••">
            </div>

            <button type="submit" class="btn-auth">S'inscrire</button>
        </form>
    </div>
</main>

<?php
// 4. Inclusion du pied de page
require_once 'includes/footer.php';
?>