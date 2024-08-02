/* écoute les alert et les referme dynamiquement */
export function clean() {
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

    // construire le HTML 
    const alertHtml = `
        <div class="${alertClasses[type]}">
            <i class="close icon"></i>
            <div class="header">
                ${message}
            </div>
        </div>
    `;
    // ajout de l'alerte au conteneur
    container.innerHTML = alertHtml;
    // ajout d'un gestionnaire d'événement pour fermer les alertes
    const closeButtons = container.querySelectorAll('.close.icon');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.parentElement.remove();
        });
    });
    // Si déjà tout en haut, ne rien faire et l'on quitte
    if (window.scrollY === 0) {
        clean();
        return;
    }

    // sinon on déplace le scrool et l'on remonte tout en haut
    container.scrollIntoView({
        behavior: 'smooth',
        block: 'start'  // début
    });
    // ajout d'un décalage de 50px après un petit délai
    setTimeout(() => {
        const offset = 50;
        const elementPosition = container.getBoundingClientRect().top - window.scrollY;
        window.scrollTo({
            top: elementPosition - offset,
            behavior: 'smooth'
        });
    }, 100);
    // on recherche les alert et les referme
    clean();
}
