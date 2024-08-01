function clean() {
    // Sélectionnez toutes les alertes Semantic UI générer par Javascript
    $('.ui.message.anim').each(function () {
        let $alert = $(this);
        // Ajouter une classe pour l'effet slide up
        $alert.addClass('closing');
        // Définir un délai pour masquer l'alerte
        setTimeout(function () {
            // Supprimer l'alerte après l'effet slide up
            $alert.slideUp('slow', function () {
                $(this).remove(); // Supprimer l'élément DOM de l'alerte
            });
        }, 3000); // Délai de 3 secondes avant de fermer l'alerte
    });
}
/**
 * Affiche une alerte dans le conteneur spécifié.
 * @param {string} type - Le type d'alerte ('positive', 'negative', 'warning').
 * @param {string} message - Le message à afficher.
 */
export function showAlert(type, message) {
    const container = document.getElementById('alert-javascript');

    // Définir les classes CSS pour chaque type d'alerte
    const alertClasses = {
        positive: 'ui positive message anim',
        negative: 'ui negative message anim',
        warning: 'ui warning message anim'
    };

    // Construire le HTML de l'alerte
    const alertHtml = `
        <div class="${alertClasses[type]}">
            <i class="close icon"></i>
            <div class="header">
                ${message}
            </div>
        </div>
    `;
    // Ajouter l'alerte au conteneur
    container.innerHTML = alertHtml;
    // Optionnel : ajouter un gestionnaire d'événement pour fermer les alertes
    const closeButtons = container.querySelectorAll('.close.icon');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.parentElement.remove();
        });
    });

    container.scrollIntoView({
        behavior: 'smooth',
        block: 'start'  // Aligne l'élément en haut du conteneur de défilement
    });
    // Ajoutez un décalage de 50px après un petit délai pour garantir le défilement initial
    setTimeout(() => {
        const offset = 100;
        const elementPosition = container.getBoundingClientRect().top - window.scrollY;
        window.scrollTo({
            top: elementPosition - offset,
            behavior: 'smooth'
        });
    }, 100); // Attendre 100ms pour garantir que le scroll initial est complété
    clean();

}
document.addEventListener('DOMContentLoaded', () => {
    console.log('=> messageFlash.js');
    // Sélectionnez toutes les alertes Semantic UI générer par PHP
    $('.ui.message.anim').each(function () {
        let $alert = $(this);
        // Ajouter une classe pour l'effet slide up
        $alert.addClass('closing');
        // Définir un délai pour masquer l'alerte
        setTimeout(function () {
            // Supprimer l'alerte après l'effet slide up
            $alert.slideUp('slow', function () {
                $(this).remove(); // Supprimer l'élément DOM de l'alerte
            });
        }, 3000); // Délai de 3 secondes avant de fermer l'alerte
    });
});
