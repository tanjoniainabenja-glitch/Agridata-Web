// Attend que TOUTE la page soit chargée (images, styles, etc.)
window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    // Ajoute la classe 'hidden' pour déclencher la transition de disparition
    preloader.classList.add('hidden');
});

// Gère le comportement des liens de démonstration
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionne tous les liens qui ne mènent nulle part pour le moment
    const placeholderLinks = document.querySelectorAll('a[href="#"], a[href^="#"]');

    placeholderLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            const href = link.getAttribute('href');
            // Si le lien est juste "#", on empêche l'action et on alerte
            if (href === '#') {
                event.preventDefault(); 
                alert('Fonctionnalité à venir ! Cette section est en cours de développement.');
            }
            // Si le lien mène à une ancre (ex: #map), le défilement fluide géré par le CSS prendra le relais
        });
    });
});