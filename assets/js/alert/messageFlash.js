// Fonction pour nettoyer les alertes
export function clean() {
    // Sélectionne toutes les alertes Semantic UI générées par Javascript
    $('.ui.message.anim.show').each(function () {
        let $alert = $(this);
        // Ajoute une classe pour l'effet slide out
        $alert.addClass('hide');
        // Supprime l'élément DOM de l'alerte après l'animation
        $alert.on('animationend', function () {
            $alert.remove();
        });
    });
}
// Fonction pour afficher une alerte
export const showAlert = (type, message) => {
    const isMobile = window.matchMedia("(max-width: 1024px)").matches;
    console.log(`Alert received: {is_mobile: ${isMobile}, type: ${type}, message: ${message}}`);

    const containerId = isMobile ? 'alert-javascript-mobile' : 'alert-javascript-desktop';
    const container = document.getElementById(containerId);

    if (!container) {
        console.error(`Container with ID ${containerId} not found.`);
        return;
    }

    const alertClasses = {
        positive: 'ui positive message anim show',
        negative: 'ui negative message anim show',
        warning: 'ui warning message anim show'
    };
    const alertHtml = `
        <div class="${alertClasses[type]}">
            <div class="header">
                ${message}
            </div>
        </div>
    `;
    container.innerHTML = alertHtml;
    // Attendre 3 secondes avant de nettoyer les alertes
    setTimeout(clean, 3000);
};
// Exécute une fois le document entièrement chargé
document.addEventListener('DOMContentLoaded', () => {
    console.log('=> messageFlash.js loaded');
    // Surveille les alertes et les nettoie après 3 secondes
    document.querySelectorAll('.ui.message.anim.show').forEach((alert) => {
        setTimeout(() => {
            // Ajouter une classe pour l'effet slide out
            alert.classList.add('hide');
            // Supprime l'élément DOM de l'alerte après l'animation
            alert.addEventListener('animationend', function () {
                alert.remove();
            });
        }, 3000);
    });
});
