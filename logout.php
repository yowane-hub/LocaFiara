<?php
/**
 * Fichier : logout.php
 * Rôle : Détruire la session en cours et rediriger vers la page de connexion.
 */
session_start(); // Reprendre la session active
session_unset(); // Vider toutes les variables de session
session_destroy(); // Détruire la session

// Redirection vers la page de connexion
header("Location: login.php");
exit();
?>