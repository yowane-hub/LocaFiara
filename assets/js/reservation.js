/**
 * Fichier : assets/js/reservation.js
 * Rôle : Validation dynamique des dates avant l'envoi du formulaire
 */

document.addEventListener('DOMContentLoaded', function() {
    const reservationForm = document.getElementById('reservationForm');
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');

    if (reservationForm) {
        reservationForm.addEventListener('submit', function(event) {
            const dateDebut = new Date(dateDebutInput.value);
            const dateFin = new Date(dateFinInput.value);

            // Vérification : la date de fin ne peut pas être avant la date de début
            if (dateDebut && dateFin && dateDebut > dateFin) {
                alert("Erreur : La date de fin ne peut pas être antérieure à la date de début.");
                event.preventDefault(); // Stoppe l'envoi du formulaire
            }
        });
    }
});