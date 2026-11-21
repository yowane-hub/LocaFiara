<?php
/**
 * Fichier : login.php
 * Rôle : Connexion utilisateur/admin + récupération de mot de passe par téléphone.
 */
session_start();
require_once 'config/db.php';

$message_erreur = "";
$message_succes = "";
$mot_de_passe_recupere = "";
$afficher_recup = isset($_POST['action_recup']) || isset($_GET['forgot']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'forgot_password') {
        $afficher_recup = true;
        $telephone = trim($_POST['telephone'] ?? '');

        if (empty($telephone)) {
            $message_erreur = "Veuillez saisir votre numéro de téléphone.";
        } else {
            $stmt = $pdo->prepare("SELECT id, prenom FROM users WHERE telephone = ?");
            $stmt->execute([$telephone]);
            $user = $stmt->fetch();

            if ($user) {
                $nouveau_mdp = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'), 0, 8);
                $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);

                $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update->execute([$hash, $user['id']]);

                $mot_de_passe_recupere = $nouveau_mdp;
                $message_succes = "Nouveau mot de passe généré pour " . htmlspecialchars($user['prenom']) . " :";
                if (empty($_POST['prenom'])) {
                    $_POST['prenom'] = $user['prenom'];
                }
            } else {
                $message_erreur = "Aucun compte trouvé avec ce numéro de téléphone.";
            }
        }
    } else {
        $prenom = trim($_POST['prenom'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($prenom) || empty($password)) {
            $message_erreur = "Tous les champs sont obligatoires.";
        } else {
            $sql = "SELECT * FROM users WHERE prenom = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$prenom]);
            $user = $stmt->fetch();

            $mdp_valide = false;
            if ($user) {
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    $mdp_valide = true;
                }
            }

            if ($mdp_valide) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $message_erreur = "Prénom ou mot de passe incorrect.";
            }
        }
    }
}

require_once 'includes/header.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/login.css">

<main>
    <div class="auth-container">
        <h1>Connexion à LocaFiara</h1>

        <?php if (!empty($message_erreur)): ?>
            <div class="alert-error"><?= htmlspecialchars($message_erreur); ?></div>
        <?php endif; ?>

        <?php if (!empty($message_succes)): ?>
            <div class="alert-success">
                <?= $message_succes; ?>
                <strong class="recovered-pass"><?= htmlspecialchars($mot_de_passe_recupere); ?></strong>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="hidden" name="action" value="login">

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required placeholder="Entrez votre prénom" class="form-control" value="<?= htmlspecialchars($_POST['prenom'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••" value="<?= htmlspecialchars($mot_de_passe_recupere); ?>">
                    <button type="button" id="togglePassword" class="eye-btn" aria-label="Afficher ou masquer le mot de passe">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="forgot-row">
                <button type="button" id="openForgotBtn" class="forgot-link">J'ai oublié mon mot de passe</button>
            </div>

            <button type="submit" class="btn-auth">Se connecter</button>
        </form>

        <div id="forgotBox" class="forgot-box <?= $afficher_recup ? 'is-open' : ''; ?>">
            <h2>Récupération par téléphone</h2>
            <p class="forgot-help">Entrez votre numéro de téléphone enregistré pour générer et afficher votre mot de passe.</p>
            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="forgot_password">
                <div class="form-group">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone" required placeholder="Ex: 0321473738" class="form-control" value="<?= htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn-auth btn-secondary-auth">Afficher mon mot de passe</button>
            </form>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/login.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('openForgotBtn');
    const forgotBox = document.getElementById('forgotBox');
    if (openBtn && forgotBox) {
        openBtn.addEventListener('click', function () {
            forgotBox.classList.toggle('is-open');
        });
    }
});
</script>