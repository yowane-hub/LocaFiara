# 🚗 LocaFiara - Système de Réservation de Véhicules à Madagascar

**LocaFiara** est une application web modulaire et professionnelle dédiée à la location de véhicules (4x4, SUV, berlines) à Madagascar. Ce projet est développé dans le cadre d'un apprentissage approfondi du développement web full-stack (PHP natif, MySQL, CSS modulaire et JavaScript).

---

## 🛠️ Stack Technique & Architecture
Le projet a été pensé sans frameworks lourds pour garantir une maîtrise totale de chaque ligne de code :
* **Backend :** PHP 8.4 (PDO pour la sécurité des requêtes).
* **Base de données :** MySQL / MariaDB.
* **Frontend :** HTML5, CSS3 pur (séparé en plusieurs fichiers modulaires).
* **Scripts :** JavaScript pur (vanilla JS) pour la gestion et la validation dynamique.

---

## 📂 Structure du Projet
```text
LocaFiara/
├── config/
│   └── db.php                  <-- Connexion sécurisée à la base de données (PDO)
├── includes/
│   ├── header.php              <-- En-tête global et barre de navigation
│   └── footer.php              <-- Pied de page global
├── assets/
│   ├── css/
│   │   ├── global.css          <-- Styles généraux et structure de base
│   │   ├── header.css          <-- Style de l'en-tête
│   │   ├── footer.css          <-- Style du pied de page
│   │   ├── index.css           <-- Style de la page d'accueil / catalogue
│   │   └── reservation.css     <-- Style de la page de réservation en 2 colonnes
│   └── js/
│       └── reservation.js      <-- Validation dynamique des formulaires
├── index.php                   <-- Catalogue des véhicules disponibles
├── reservation.php             <-- Page de détail et formulaire de réservation
└── traitement_reservation.php  <-- Script PHP de traitement et d'insertion sécurisée en BDD

🚀 Fonctionnalités Réalisées (Jour 1)

    Architecture modulaire : Séparation propre des composants HTML, des feuilles de style CSS et des scripts JS.

    Catalogue dynamique (index.php) : Connexion à la base de données pour lister en temps réel les véhicules disponibles avec leurs catégories et tarifs.

    Page de réservation sur mesure (reservation.php) :

        Affichage en 2 colonnes (carte du véhicule d'un côté, formulaire de l'autre).

        Design soigné aux couleurs de LocaFiara (bleu profond et touches de jaune).

    Traitement sécurisé (traitement_reservation.php) :

        Vérification de la cohérence des dates de début et de fin.

        Calcul automatique de la durée de location en jours.

        Calcul dynamique du montant total (incluant l'option chauffeur journalière).

        Enregistrement sécurisé des données dans la table reservations via des requêtes préparées PDO.


📌 À Faire / Prochaines Étapes (Pour Demain)

    Amélioration du Frontend (JS) : Calculer et afficher le prix total en direct sur la page de réservation dès que l'utilisateur modifie les dates ou coche l'option chauffeur.

    Gestion des utilisateurs : Mettre en place un système de connexion (login.php) pour lier dynamiquement les réservations au véritable user_id de la session.

    Espace Client / Administration : Créer une interface pour visualiser la liste des réservations en cours et modifier leur statut.