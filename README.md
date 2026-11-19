# 🚗 LocaFiara

**LocaFiara** est une application web de **location de véhicules** pensée pour Madagascar (agence basée à Antsirabe). Un client peut consulter le catalogue, réserver un véhicule (avec ou sans chauffeur), payer par **Mobile Money** (MVola, Orange Money, Airtel Money) ou en espèces avec acompte, puis télécharger sa facture. Un administrateur gère les véhicules, les chauffeurs, les réservations et valide les paiements.

Le projet est écrit en **PHP pur, MySQL/MariaDB, HTML, CSS et JavaScript**, sans framework. Il a été réalisé dans le cadre d'un apprentissage du développement web full-stack, et son code est volontairement simple et commenté pour être lisible par un débutant.

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/CSS3-Responsive-1572B6?logo=css3&logoColor=white)

---

## 📑 Sommaire

1. [Aperçu du projet](#-aperçu-du-projet)
2. [Fonctionnalités](#-fonctionnalités)
3. [Technologies utilisées](#-technologies-utilisées)
4. [Structure du projet](#-structure-du-projet)
5. [Installation pas à pas](#-installation-pas-à-pas)
6. [Création de la base de données](#-création-de-la-base-de-données)
7. [Configuration (base de données et e-mails)](#-configuration)
8. [Créer le premier compte administrateur](#-créer-le-premier-compte-administrateur)
9. [Comment utiliser l'application](#-comment-utiliser-lapplication)
10. [Règles de gestion (prix, acompte, paiement)](#-règles-de-gestion)
11. [Sécurité : ce qui est déjà en place et ce qu'il reste à faire](#-sécurité)
12. [Design responsive et mode sombre](#-design-responsive-et-mode-sombre)
13. [Problèmes fréquents](#-problèmes-fréquents)
14. [Idées d'améliorations](#-idées-daméliorations)
15. [Publier le projet sur GitHub](#-publier-le-projet-sur-github)
16. [Auteur et licence](#-auteur-et-licence)

---

## 🔎 Aperçu du projet

Le parcours d'un client se résume ainsi :

```
Inscription ──► Connexion ──► Choix d'un véhicule ──► Réservation
                                                          │
                       Facture PDF ◄── Validation admin ◄─┴─► Paiement (Mobile Money / Espèces)
```

Il existe **deux types d'utilisateurs** :

| Rôle | Ce qu'il peut faire |
|------|---------------------|
| **Client** (`client`) | S'inscrire, se connecter, voir les véhicules, réserver, payer, télécharger sa facture, laisser un avis |
| **Administrateur** (`admin`) | Tout gérer : véhicules, chauffeurs, statut des réservations, validation des paiements |

---

## ✨ Fonctionnalités

### 👤 Espace client

- **Inscription** avec contrôle du mot de passe (8 caractères minimum, 1 majuscule, 1 chiffre). Le mot de passe est **haché** : il n'est jamais stocké en clair.
- **Connexion / déconnexion** avec gestion de session, et bouton « œil » pour afficher ou masquer le mot de passe.
- **Catalogue des véhicules** (`vehicules.php`) avec **filtre par catégorie** (voitures, SUV, motos, camions…). Seuls les véhicules `disponible` sont affichés.
- **Réservation** (`reservation.php`) :
  - choix de la date de début et de fin ;
  - option **chauffeur** (+ 20 000 Ar par jour) via une liste déroulante ;
  - **calcul du prix en direct** en JavaScript, sans recharger la page.
- **Mes réservations** (`mes_reservations.php`) : historique, statut coloré (`en_attente`, `confirmee`, `annulee`), chauffeur assigné, montant.
- **Paiement** (`payer.php`) :
  - **Mobile Money (100 %)** : le client envoie l'argent vers un numéro marchand de l'agence puis saisit la **référence de transaction** reçue par SMS ;
  - **Espèces** : un **acompte de 50 %** doit d'abord être payé par Mobile Money, le reste est payé à l'agence à la remise des clés.
- **Annulation** de la réservation si le client refuse les conditions de paiement.
- **Facture imprimable / PDF** (`telecharger_facture.php`) : disponible après validation du paiement par l'admin (bouton « Imprimer en PDF » du navigateur).
- **Avis clients** : note de 1 à 5 étoiles et commentaire sur une réservation confirmée.
- **Mode sombre / clair** mémorisé dans le navigateur.

### 🛠️ Espace administrateur

- **Tableau de bord** (`admin_dashboard.php`) : nombre de réservations, chiffre d'affaires, liste de toutes les réservations et **changement de statut** (en attente / confirmée / annulée).
- **Gestion des véhicules** (`admin_vehicule.php`) : ajouter, modifier, supprimer, avec **photo** et catégorie.
- **Gestion des chauffeurs** (`admin_drivers.php`) : ajouter, modifier, supprimer, statut (`disponible` / `en_mission`).
- **Gestion des paiements** (`admin_paiements.php`) :
  - liste des paiements (les « en attente » en premier) ;
  - bouton **Valider** ou **Refuser** ;
  - à la validation : **création automatique de la facture** (`FACT-ANNÉE-NUMÉRO`) et **envoi d'un e-mail de confirmation** au client avec PHPMailer.

---

## 💻 Technologies utilisées

| Domaine | Outil | Rôle |
|---------|-------|------|
| Serveur | **PHP 8+** | Logique de l'application |
| Base de données | **MySQL / MariaDB** (via **PDO**) | Stockage des données, requêtes préparées contre les injections SQL |
| Interface | **HTML5**, **CSS3** (Grid, Flexbox, variables CSS) | Pages et design responsive |
| Interactivité | **JavaScript** (sans bibliothèque) | Calcul du prix, mode sombre, œil du mot de passe, choix du paiement |
| E-mails | **PHPMailer** (dossier `libs/`) + SMTP Gmail | Confirmation de paiement |
| Versionnement | **Git** et **GitHub** | Suivi et publication du code |

---

## 📂 Structure du projet

```
locafiara/
│
├── index.php                  # Page d'accueil
├── vehicules.php              # Catalogue public + filtres par catégorie
├── register.php               # Inscription
├── login.php                  # Connexion
├── logout.php                 # Déconnexion
│
├── reservation.php            # Formulaire de réservation
├── traitement_reservation.php # Enregistre la réservation en base
├── mes_reservations.php       # Historique des réservations du client
├── annuler_reservation.php    # Annule une réservation
├── payer.php                  # Page de paiement
├── traitement_paiement.php    # Enregistre la demande de paiement
├── traitement_avis.php        # Enregistre un avis client
├── telecharger_facture.php    # Facture imprimable / PDF
│
├── admin_dashboard.php        # Tableau de bord admin
├── admin_vehicule.php         # Gestion des véhicules
├── admin_drivers.php          # Gestion des chauffeurs
├── admin_paiements.php        # Validation des paiements + e-mails
│
├── config/
│   ├── db.php                 # Connexion à la base (PDO)  ⚠️ à ne pas publier
│   └── mail.php               # Envoi d'e-mails (PHPMailer) ⚠️ à ne pas publier
│
├── includes/
│   ├── header.php             # En-tête du site public
│   ├── admin_header.php       # En-tête de l'espace admin
│   └── footer.php             # Pied de page
│
├── libs/                      # Bibliothèque PHPMailer (ne pas modifier)
│
└── assets/
    ├── css/                   # Un fichier CSS par page + global.css
    ├── js/
    │   ├── reservation.js     # Calcul du prix en direct
    │   ├── login.js           # Afficher / masquer le mot de passe
    │   └── theme.js           # Mode sombre / clair
    └── image/                 # Images du site
```

> 💡 **Astuce débutant :** les fichiers `traitement_*.php` ne s'affichent pas à l'écran. Ils reçoivent les données d'un formulaire, les enregistrent dans la base, puis redirigent l'utilisateur vers une autre page.

---

## 🚀 Installation pas à pas

### Ce dont vous avez besoin

- **PHP 8.0 ou plus** et **MySQL / MariaDB**.
- Le plus simple pour débuter : installer **XAMPP** (Windows, macOS, Linux) ou **Laragon** (Windows). Ils installent Apache, PHP et MySQL en un seul clic.
- Un éditeur de code (par exemple **VS Code**) et **Git**.

### Étapes

**1. Récupérer le projet**

```bash
git clone https://github.com/VOTRE-NOM-UTILISATEUR/locafiara.git
```

*(ou téléchargez le ZIP depuis GitHub puis décompressez-le).*

**2. Placer le dossier au bon endroit**

| Outil | Dossier où copier le projet |
|-------|-----------------------------|
| XAMPP | `C:\xampp\htdocs\locafiara` (Linux : `/opt/lampp/htdocs/locafiara`) |
| Laragon | `C:\laragon\www\locafiara` |
| Linux (Apache) | `/var/www/html/locafiara` |

**3. Démarrer Apache et MySQL** depuis le panneau de contrôle de XAMPP / Laragon.

**4. Créer la base de données** : voir la section suivante.

**5. Configurer la connexion** dans `config/db.php` : voir [Configuration](#-configuration).

**6. Ouvrir le site** dans le navigateur : <http://localhost/locafiara/>

---

## 🗄️ Création de la base de données

1. Ouvrez **phpMyAdmin** : <http://localhost/phpmyadmin>
2. Cliquez sur **SQL** et collez le script ci-dessous. Il crée la base `locafiara` et toutes les tables utilisées par le code.

> ℹ️ Ce script a été reconstitué à partir des requêtes SQL du projet. Si votre base actuelle contient des colonnes supplémentaires, vous pouvez l'exporter depuis phpMyAdmin (**Exporter → SQL**) et ajouter ce fichier au dépôt sous `database/locafiara.sql`.

```sql
CREATE DATABASE IF NOT EXISTS locafiara
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE locafiara;

-- Utilisateurs (clients et administrateurs)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  telephone VARCHAR(30) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('client','admin') NOT NULL DEFAULT 'client',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Catégories de véhicules
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL
);

-- Véhicules (l'image est stockée directement en base)
CREATE TABLE vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT,
  immatriculation VARCHAR(30) NOT NULL,
  marque VARCHAR(100) NOT NULL,
  modele VARCHAR(100) NOT NULL,
  prix_par_jour DECIMAL(12,2) NOT NULL,
  statut VARCHAR(30) NOT NULL DEFAULT 'disponible',
  image LONGBLOB NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Chauffeurs
CREATE TABLE drivers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  telephone VARCHAR(30) NOT NULL,
  statut VARCHAR(30) NOT NULL DEFAULT 'disponible'
);

-- Réservations
CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  vehicle_id INT NOT NULL,
  driver_id INT NULL,
  ville_depart VARCHAR(100),
  ville_restitution VARCHAR(100),
  date_debut DATE NOT NULL,
  date_fin DATE NOT NULL,
  montant_total DECIMAL(12,2) NOT NULL,
  montant_acompte DECIMAL(12,2) DEFAULT 0,
  montant_solde DECIMAL(12,2) DEFAULT 0,
  montant_caution DECIMAL(12,2) DEFAULT 0,
  statut_reservation VARCHAR(30) NOT NULL DEFAULT 'en_attente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
  FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- Numéros marchands de l'agence (MVola, Orange Money, Airtel Money)
CREATE TABLE marchands (
  id INT AUTO_INCREMENT PRIMARY KEY,
  operateur VARCHAR(50) NOT NULL,
  numero VARCHAR(30) NOT NULL,
  titulaire VARCHAR(100) NOT NULL
);

-- Paiements envoyés par les clients
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL,
  mode_paiement VARCHAR(30) NOT NULL,
  type_paiement VARCHAR(30) NOT NULL,          -- 'total' ou 'acompte_50'
  reference_transaction VARCHAR(100) NOT NULL,
  montant DECIMAL(12,2) NOT NULL,
  statut_paiement VARCHAR(30) NOT NULL DEFAULT 'en_attente',  -- en_attente / valide / refuse
  date_paiement DATETIME NOT NULL,
  FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);

-- Factures (une seule par réservation)
CREATE TABLE invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL UNIQUE,
  numero_facture VARCHAR(50) NOT NULL,
  montant_ttc DECIMAL(12,2) NOT NULL,
  date_emission TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);

-- Avis clients
CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL,
  user_id INT NOT NULL,
  note TINYINT NOT NULL,
  commentaire TEXT NOT NULL,
  date_avis DATETIME NOT NULL,
  FOREIGN KEY (reservation_id) REFERENCES reservations(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Données de départ pour tester
INSERT INTO categories (nom) VALUES ('Citadine'), ('SUV'), ('Moto'), ('Camion');

INSERT INTO marchands (operateur, numero, titulaire) VALUES
  ('MVola', '034 00 000 00', 'LocaFiara'),
  ('Orange Money', '032 00 000 00', 'LocaFiara'),
  ('Airtel Money', '033 00 000 00', 'LocaFiara');
```

> ⚠️ Remplacez les numéros marchands d'exemple par les vrais numéros de votre agence.

---

## ⚙️ Configuration

### 1. Connexion à la base de données : `config/db.php`

Ouvrez `config/db.php` et adaptez ces quatre lignes :

```php
$host     = 'localhost';      // En local : localhost
$dbname   = 'locafiara';      // Nom de la base créée plus haut
$username = 'root';           // Utilisateur MySQL (root par défaut sous XAMPP)
$password = '';               // Mot de passe (vide par défaut sous XAMPP)
```

> En production (par exemple sur InfinityFree), utilisez les identifiants fournis par votre hébergeur.

### 2. Envoi d'e-mails : `config/mail.php`

L'application envoie un e-mail au client lorsque l'admin valide un paiement. Elle utilise **Gmail** via PHPMailer. Pour que cela fonctionne :

1. Activez la **validation en deux étapes** sur le compte Gmail qui enverra les messages.
2. Créez un **mot de passe d'application** (Compte Google → Sécurité → Mots de passe des applications).
3. Dans `config/mail.php`, renseignez l'adresse Gmail comme `Username` (et dans `setFrom`) et le **mot de passe d'application de 16 caractères** comme `Password`.

> ❗ N'utilisez **jamais** votre vrai mot de passe Gmail ici, uniquement un mot de passe d'application.
> Si l'envoi échoue, la validation du paiement fonctionne quand même : un message d'avertissement s'affiche à l'admin.

### 3. ⛔ Ne publiez jamais vos mots de passe sur GitHub

Les fichiers `config/db.php` et `config/mail.php` contiennent des **identifiants secrets**. Une fois sur GitHub, ils seraient visibles par tout le monde. Voir la section [Publier sur GitHub](#-publier-le-projet-sur-github) pour la bonne méthode.

---

## 👑 Créer le premier compte administrateur

L'inscription crée toujours un compte **client**. Pour avoir un administrateur :

1. Inscrivez-vous normalement sur `register.php`.
2. Dans phpMyAdmin, ouvrez la table `users`, puis exécutez (en remplaçant l'adresse e-mail) :

```sql
UPDATE users SET role = 'admin' WHERE email = 'votre@email.com';
```

3. Déconnectez-vous puis reconnectez-vous : vous serez redirigé vers le **tableau de bord administrateur**.

> 📝 La connexion se fait avec le **prénom** et le mot de passe (et non l'e-mail).

---

## 📖 Comment utiliser l'application

### En tant que client

1. **Créez un compte** (bouton *Inscription*), puis **connectez-vous**.
2. Allez dans **Nos Véhicules**, filtrez par catégorie si besoin, puis cliquez sur **Réserver**.
3. Choisissez vos dates et, si vous le souhaitez, un chauffeur. Le **total s'affiche en direct**.
4. Validez : la réservation apparaît dans **Mes Réservations** avec le statut `en_attente`.
5. Quand l'agence passe la réservation à `confirmee`, le bouton **Procéder au paiement** apparaît.
6. Choisissez Mobile Money ou Espèces, faites le transfert vers un numéro marchand, saisissez la **référence du SMS** et envoyez.
7. Après validation par l'admin, vous recevez un **e-mail** et pouvez **télécharger votre facture**.

### En tant qu'administrateur

1. Connectez-vous : vous arrivez sur le **tableau de bord**.
2. **Réservations** : changez le statut avec la liste déroulante puis *Mettre à jour*.
3. **Véhicules** et **Chauffeurs** : utilisez les boutons de la barre rapide pour ajouter, modifier ou supprimer.
4. **Paiements** : vérifiez que la référence correspond bien à un transfert reçu sur votre compte marchand, puis cliquez sur **Valider** (facture + e-mail automatiques) ou **Refuser**.

---

## 📐 Règles de gestion

| Règle | Détail |
|-------|--------|
| Durée de location | Nombre de jours entre les deux dates, **premier jour inclus** (du 1er au 3 = 3 jours) |
| Prix | `jours × prix du véhicule par jour` |
| Option chauffeur | **+ 20 000 Ar par jour** |
| Montant total | Toujours **recalculé côté serveur** à partir du prix en base (le client ne peut pas le modifier) |
| Paiement Mobile Money | **100 %** du montant, vérifié puis validé par l'admin |
| Paiement en espèces | **Acompte de 50 %** par Mobile Money, le reste en espèces à l'agence |
| Statuts de réservation | `en_attente` → `confirmee` ou `annulee` |
| Statuts de paiement | `en_attente` → `valide` ou `refuse` |
| Numéro de facture | `FACT-ANNÉE-NUMÉRO DE RÉSERVATION` (ex. `FACT-2026-0012`) |

---

## 🔐 Sécurité

### ✅ Déjà en place

- **Mots de passe hachés** avec `password_hash()` et vérifiés avec `password_verify()`.
- **Requêtes préparées PDO** : protection contre les injections SQL.
- **`htmlspecialchars()`** sur les données affichées : protection contre les failles XSS.
- **Sessions PHP** et redirection vers la connexion pour les pages privées.
- Un client ne peut accéder qu'à **ses propres** réservations, paiements et factures.
- Prix recalculé côté serveur.

### ⚠️ À renforcer (bon exercice pour progresser)

Ces points sont connus et à corriger avant une vraie mise en production :

1. **Protéger toutes les pages admin par le rôle.** Aujourd'hui, seule `admin_paiements.php` vérifie que l'utilisateur est `admin`. Les pages `admin_dashboard.php`, `admin_vehicule.php` et `admin_drivers.php` devraient faire la même vérification :
   ```php
   if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
       header("Location: login.php");
       exit();
   }
   ```
2. **Ne pas publier les identifiants** (`config/db.php`, `config/mail.php`) et changer ceux déjà publiés.
3. **Supprimer via POST** plutôt que par un lien `?action=delete` (et ajouter un jeton CSRF).
4. **Valider les fichiers envoyés** (type et taille des images de véhicules).
5. **Masquer les messages d'erreur SQL** à l'utilisateur en production (les écrire dans un fichier de log).
6. **Vérifier la disponibilité** d'un véhicule sur les dates choisies (éviter les doubles réservations).
7. **Connexion par e-mail** plutôt que par prénom (deux clients peuvent avoir le même prénom).

---

## 📱 Design responsive et mode sombre

- Chaque fichier CSS contient un bloc `@media` à la fin, avec trois paliers :
  - **≤ 992 px** : petites tablettes ;
  - **≤ 768 px** : tablettes en mode portrait ;
  - **≤ 480 px** : smartphones.
- Sur mobile : menus empilés, grilles sur une colonne, boutons pleine largeur, tableau des paiements défilant horizontalement, champs de formulaire à 16 px pour éviter le zoom automatique sur iPhone.
- La facture reste identique à l'impression (les règles responsive ne s'appliquent qu'à l'écran).
- **Mode sombre** : le bouton 🌙 / ☀️ du menu bascule le thème, mémorisé dans le navigateur grâce à `localStorage`. Les couleurs sont gérées par des **variables CSS** dans `assets/css/global.css`.

> Chaque page doit contenir `<meta name="viewport" content="width=device-width, initial-scale=1">` dans son `<head>` (c'est déjà le cas dans les en-têtes du projet).

---

## 🧯 Problèmes fréquents

| Symptôme | Cause probable | Solution |
|----------|----------------|----------|
| `Erreur de connexion à la base de données` | Mauvais identifiants ou MySQL arrêté | Vérifiez `config/db.php` et démarrez MySQL |
| Page blanche | Erreur PHP masquée | Activez l'affichage des erreurs en local : `ini_set('display_errors', 1);` |
| `Table '...' doesn't exist` | Script SQL non exécuté | Exécutez le script de la section base de données |
| L'e-mail n'est pas envoyé | Mot de passe d'application incorrect, port 587 bloqué, ou hébergeur gratuit qui bloque SMTP | Vérifiez `config/mail.php` ; testez en local |
| Le design ne s'adapte pas au téléphone | Balise `viewport` manquante ou cache du navigateur | Ajoutez la balise puis rechargez avec `Ctrl + F5` |
| Impossible de supprimer un véhicule ou un chauffeur | Il est lié à une réservation | Changez son statut au lieu de le supprimer |
| Photo de véhicule trop lourde | Limite d'envoi de PHP | Augmentez `upload_max_filesize` et `post_max_size` dans `php.ini`, ou compressez l'image |

---

## 🌱 Idées d'améliorations

- Calendrier de disponibilité et blocage des dates déjà réservées
- Paiement automatique via l'API des opérateurs Mobile Money
- Vraie génération de PDF (bibliothèque comme Dompdf) et envoi de la facture en pièce jointe
- Mot de passe oublié et vérification de l'adresse e-mail
- Affichage des avis sur la fiche de chaque véhicule
- Recherche et filtres avancés (prix, nombre de places, carburant)
- Interface multilingue (français / malgache)
- Passage à l'architecture MVC puis à un framework (Laravel, Symfony)
- Tests automatisés

---

## 📤 Publier le projet sur GitHub

### 1. Protéger vos mots de passe avec `.gitignore`

À la racine du projet, créez un fichier nommé `.gitignore` contenant :

```
config/db.php
config/mail.php
```

Puis créez deux **fichiers modèles** (sans mot de passe) à publier à leur place, par exemple `config/db.example.php` et `config/mail.example.php`. Chaque personne qui installe le projet les copie, retire `.example` du nom, puis remplit ses propres informations.

> ⚠️ Si vos identifiants ont **déjà été publiés** sur GitHub, considérez-les comme compromis : **changez le mot de passe de la base de données et celui de l'application Gmail**, même après suppression du fichier (l'historique Git le conserve).
> Pour retirer un fichier déjà suivi par Git : `git rm --cached config/db.php config/mail.php`

### 2. Envoyer les modifications

```bash
git status                                  # voir ce qui a changé
git add .                                   # préparer tous les fichiers
git commit -m "Mise à jour : README, design responsive"
git push origin main                        # envoyer sur GitHub (ou master selon votre branche)
```

### 3. Première publication d'un nouveau dépôt

```bash
git init
git add .
git commit -m "Premier commit : LocaFiara"
git branch -M main
git remote add origin https://github.com/VOTRE-NOM-UTILISATEUR/locafiara.git
git push -u origin main
```

---

## 👨‍💻 Auteur et licence

Projet réalisé par **[Votre Nom]** dans le cadre d'un apprentissage du développement web full-stack.

- GitHub : [@votre-nom-utilisateur](https://github.com/votre-nom-utilisateur)
- Contact : votre.email@exemple.com

Ce projet est destiné à l'apprentissage. Vous pouvez ajouter un fichier `LICENSE` (par exemple la licence **MIT**) si vous souhaitez autoriser d'autres personnes à réutiliser le code.

---

<p align="center">Fait avec ❤️ à Madagascar 🇲🇬 — <strong>🚗 Loca<span>Fiara</span></strong></p>