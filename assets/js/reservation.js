/**
 * Fichier : assets/js/reservation.js
 * Rôle : Gérer l'interactivité, calculer le prix en temps réel et valider les dates
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Récupération des éléments du formulaire par leurs ID
    const form          = document.getElementById('reservationForm');
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput   = document.getElementById('date_fin');
    const chauffeurCheckbox = document.getElementById('avec_chauffeur');
    
    // Élément HTML où l'on va afficher le prix total calculé en direct
    const spanPrixTotal = document.getElementById('prix_total_affichage');

    // Récupération du prix journalier transmis par PHP (injecté dans la page)
    // S'il n'existe pas, on met 0 par sécurité
    const prixParJour = window.prixVehiculeJour || 0;

    /**
     * Fonction principale pour calculer et afficher le prix total
     */
    function calculerPrixTotal() {
        // On récupère les valeurs des dates
        const valeurDebut = dateDebutInput.value;
        const valeurFin = dateFinInput.value;

        // Si l'une des deux dates est vide, on arrête le calcul
        if (!valeurDebut || !valeurFin) {
            spanPrixTotal.textContent = "0 Ar";
            return;
        }

        // Conversion des chaînes de caractères en objets Date JavaScript
        const dateDebut = new Date(valeurDebut);
        const dateFin = new Date(valeurFin);

        // Calcul de la différence en millisecondes, convertie ensuite en jours
        const differenceTemps = dateFin.getTime() - dateDebut.getTime();
        const nbJours = (differenceTemps / (1000 * 3600 * 24)) + 1; // +1 pour inclure le premier jour

        // Vérification de la cohérence des dates
        if (nbJours <= 0) {
            spanPrixTotal.textContent = "Dates invalides";
            return;
        }

        document.getElementById('affichage_duree').textContent="Durée : "+nbJours+" jour(s)";

        // Calcul du montant de base de la location
        let montantVehicule = nbJours * prixParJour;

        // Calcul de l'option chauffeur (+ 20 000 Ar par jour si la case est cochée)
        let montantChauffeur = 0;
        if (chauffeurCheckbox.checked) {
            montantChauffeur = nbJours * 20000;
        }

        // Calcul du montant total final
        let prixTotal = montantVehicule + montantChauffeur;

        // Affichage du résultat formaté proprement (avec des espaces pour les milliers)
        spanPrixTotal.textContent = prixTotal.toLocaleString('fr-FR') + " Ar ";


    }

    // 2. Écouteurs d'événements : Dès que l'utilisateur change une date ou clique sur la case, on recalcule !
    if (dateDebutInput && dateFinInput) {
        dateDebutInput.addEventListener('change', calculerPrixTotal);
        dateFinInput.addEventListener('change', calculerPrixTotal);
        chauffeurCheckbox.addEventListener('change', calculerPrixTotal);
    }

    // 3. Sécurité supplémentaire lors de la soumission du formulaire
    if (form) {
        form.addEventListener('submit', function(event) {
            const dateDebut = new Date(dateDebutInput.value);
            const dateFin = new Date(dateFinInput.value);

            if (dateDebut > dateFin) {
                alert("Erreur : La date de fin ne peut pas être antérieure à la date de début.");
                event.preventDefault(); // Bloque l'envoi du formulaire
            }
        });
    }
});