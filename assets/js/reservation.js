document.addEventListener('DOMContentLoaded', function () {
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');
    const avecChauffeurCheckbox = document.getElementById('avec_chauffeur');
    const prixTotalAffichage = document.getElementById('prix_total_affichage');
    const affichageDuree = document.getElementById('affichage_duree');

    function calculerPrix() {
        if (!dateDebutInput.value || !dateFinInput.value) {
            return;
        }

        const dateDebut = new Date(dateDebutInput.value);
        const dateFin = new Date(dateFinInput.value);

        if (dateFin >= dateDebut) {
            const diffTime = Math.abs(dateFin - dateDebut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Inclut le premier jour

            // Récupération du prix journalier de la voiture
            let prixJourTotal = window.prixVehiculeJour || 0;

            // Ajout de l'option chauffeur si cochée (+20 000 Ar par jour)
            if (avecChauffeurCheckbox && avecChauffeurCheckbox.checked) {
                prixJourTotal += 20000;
            }

            const total = diffDays * prixJourTotal;

            if (affichageDuree) {
                affichageDuree.textContent = `Durée : ${diffDays} jour(s)`;
            }
            if (prixTotalAffichage) {
                prixTotalAffichage.textContent = total.toLocaleString('fr-FR') + ' Ar';
            }
        } else {
            if (affichageDuree) {
                affichageDuree.textContent = 'Erreur : La date de fin doit être après la date de début';
            }
            if (prixTotalAffichage) {
                prixTotalAffichage.textContent = '0 Ar';
            }
        }
    }

    if (dateDebutInput && dateFinInput) {
        dateDebutInput.addEventListener('change', calculerPrix);
        dateFinInput.addEventListener('change', calculerPrix);
    }
    
    if (avecChauffeurCheckbox) {
        avecChauffeurCheckbox.addEventListener('change', calculerPrix);
    }
});