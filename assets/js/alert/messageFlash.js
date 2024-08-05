export function clean() {
    // Sélectionnez toutes les alertes Semantic UI générées par Javascript
    $('.ui.message.anim.show').each(function () {
        let $alert = $(this);
        // Ajouter une classe pour l'effet slide out
        $alert.addClass('hide');
        // Supprimer l'élément DOM de l'alerte après l'animation
        $alert.on('animationend', function () {
            $alert.remove();
        });
    });
}

export const showAlert = (type, message) => {
    const isMobile = window.matchMedia("(max-width: 1024px)").matches;
    const containerId = isMobile ? 'alert-javascript-mobile' : 'alert-javascript-desktop';
    const container = document.getElementById(containerId);

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
document.addEventListener('DOMContentLoaded', () => {
    console.log('=> messageFlash.js loaded');
    // on surveille les alert
    document.querySelectorAll('.ui.message.anim.show').forEach((alert) => {
        setTimeout(() => {
            // Ajouter une classe pour l'effet slide out
            alert.classList.add('hide');
            // Supprimer l'élément DOM de l'alerte après l'animation
            alert.addEventListener('animationend', function () {
                alert.remove();
            });
        }, 3000);
    });
});