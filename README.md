# 🚗 LocaFiara

**LocaFiara** est une plateforme web professionnelle de location de véhicules conçue sur mesure pour le marché de Madagascar. Elle permet de consulter un catalogue dynamique de véhicules (4x4, SUV, berlines), de gérer les réservations, les options de chauffeurs et le calcul des cautions et acomptes.

## 🛠️ Stack Technique

* **Langage Backend :** PHP 8.4 (Utilisation de PDO pour la sécurité des requêtes)
* **Base de données :** MySQL / MariaDB
* **Design & Style :** Tailwind CSS (compilé en local via Tailwind CLI pour un fonctionnement 100% hors-ligne)
* **Environnement de développement :** Kali Linux (Apache2, LAMP Stack)

## 📁 Architecture du Projet (MVC-lite)

```text
LocaFiara/
├── assets/
│   └── css/
│       ├── input.css       # Fichier source Tailwind
│       └── tailwind.css    # Fichier CSS compilé et utilisé par l'application
├── config/
│   └── db.php              # Connexion centralisée à la base de données via PDO
├── includes/
│   ├── header.php          # En-tête et barre de navigation réutilisables
│   └── footer.php          # Pied de page réutilisable
├── index.php               # Page d'accueil et catalogue dynamique des véhicules
└── README.md               # Documentation du projet