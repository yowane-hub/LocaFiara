/**
 * Fichier : assets/js/theme.js
 * Rôle : Gestion du basculement et de la persistance du Mode Sombre / Clair
 */

document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    // 1. Vérifier si un thème était déjà enregistré dans le navigateur (localStorage)
    const savedTheme = localStorage.getItem('locafiara_theme') || 'light';
    htmlElement.setAttribute('data-theme', savedTheme);
    updateButtonIcon(savedTheme);

    // 2. Écouter le clic sur le bouton
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            // Appliquer le nouveau thème
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('locafiara_theme', newTheme);
            updateButtonIcon(newTheme);
        });
    }

    // Fonction pour changer l'icône du bouton (🌙 / ☀️)
    function updateButtonIcon(theme) {
        if (themeToggleBtn) {
            themeToggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
        }
    }
});