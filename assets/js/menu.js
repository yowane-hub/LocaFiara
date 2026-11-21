/**
 * Fichier : assets/js/menu.js
 * Rôle : Ouvrir / fermer le menu "..." sur téléphone (en-tête public et admin)
 */
document.addEventListener('DOMContentLoaded', function () {
    const bouton = document.getElementById('menu-toggle');
    const menu = document.getElementById('main-nav');
 
    if (!bouton || !menu) return;
 
    function ouvrir() {
        menu.classList.add('open');
        bouton.setAttribute('aria-expanded', 'true');
    }
 
    function fermer() {
        menu.classList.remove('open');
        bouton.setAttribute('aria-expanded', 'false');
    }
 
    // Clic sur le bouton "..." : ouvre ou ferme le menu
    bouton.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.contains('open') ? fermer() : ouvrir();
    });
 
    // Clic sur un lien du menu : on referme
    menu.querySelectorAll('a').forEach(function (lien) {
        lien.addEventListener('click', fermer);
    });
 
    // Clic en dehors du menu : on referme
    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target) && !bouton.contains(e.target)) fermer();
    });
 
    // Touche Échap : on referme
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') fermer();
    });
 
    // Retour à la taille ordinateur : on remet tout à zéro
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) fermer();
    });
});
 