# LocaFiara 🚗

**LocaFiara** est une application web professionnelle de location de véhicules développée sur mesure pour le marché à Madagascar. Ce projet est réalisé dans le cadre d'un apprentissage approfondi du développement full-stack (PHP, MySQL, JavaScript).

---

## 🛠️ Architecture du Projet

Le projet adopte une architecture modulaire et propre, sans framework lourd, reposant sur du code pur et pédagogique :

- `/assets/css/` : Fichiers de styles CSS séparés pour chaque page (`global.css`, `index.css`, `reservation.css`, `mes_reservations.css`).
- `/assets/js/` : Scripts JavaScript pour l'interactivité côté client (`reservation.js`).
- `/config/` : Configuration de la connexion à la base de données via PDO (`db.php`).
- `/includes/` : Éléments réutilisables du site (`header.php`, `footer.php`).
- Fichiers principaux à la racine : `index.php`, `reservation.php`, `traitement_reservation.php`, `mes_reservations.php`.

---

## ✨ Fonctionnalités Actuelles

1. **Catalogue Dynamique (`index.php`) :**
   - Affichage en temps réel des véhicules disponibles depuis la base de données MariaDB.
   - Présentation claire des tarifs journaliers et des catégories.

2. **Formulaire de Réservation Interactif (`reservation.php`) :**
   - Sélection des dates de début et de fin.
   - Option d'ajout de chauffeur (+ 20 000 Ar/jour).
   - **Calcul dynamique en direct (JavaScript)** du montant total de la location et de la durée en jours sans recharger la page.

3. **Traitement Sécurisé des Données (`traitement_reservation.php`) :**
   - Validation des données en PHP/PDO.
   - Enregistrement automatique des réservations avec calcul de l'acompte (30%).

4. **Espace "Mes Réservations" (`mes_reservations.php`) :**
   - Grille fluide et responsive présentant l'historique des réservations de l'utilisateur.
   - **Badges de statut dynamiques** personnalisés (`en_attente`, `confirmee`, `annulee`).
   - Compteur global du nombre de réservations.

---

## 💻 Technologies Utilisées

- **Côté Serveur (Backend) :** PHP (avec PDO pour la sécurité des requêtes SQL)
- **Base de données :** MariaDB / MySQL
- **Côté Client (Frontend) :** HTML5, CSS3 pur (grilles CSS, design moderne), JavaScript (Vanilla)
- **Version de travail :** Linux (Kali Linux), Git & GitHub pour le contrôle de version en ligne de commande.