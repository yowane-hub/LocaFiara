<?php
/**
 * Fichier : config/db.php
 * Rôle : Établit la connexion sécurisée à la base de données MySQL via PDO
 */

$host = 'localhost';       // Adresse du serveur de base de données
$dbname = 'LocaFiara';    // Nom de la base de données du projet
$username = 'root';      // Nom d'utilisateur MySQL par défaut sous Kali Linux
$password = 'yowane1234';         // Mot de passe

try {
    // Initialisation de la connexion PDO avec encodage UTF-8 et gestion rigoureuse des erreurs
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username,$password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,     // Active les exceptions en cas d'erreur SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Récupère les résultats sous forme de tableaux associatifs
    ]);
    
} catch (PDOException $e) {
    // En cas d'échec, on stoppe l'exécution et on affiche un message propre
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>