export function clean() {
    // Sélectionnez toutes les alertes Semantic UI générées par Javascript
    $('.ui.message.anim').each(function () {
        let $alert = $(this);

        // Ajouter une classe pour l'effet slide out
        $alert.addClass('hide');

        // Supprimer l'élément DOM de l'alerte après l'animation
        $alert.on('animationend', function() {
            $alert.remove();
        });
    });
}

export const showAlert = (type, message) => {
    const isMobile = window.matchMedia("(max-width: 768px)").matches;
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

    const closeButtons = container.querySelectorAll('.close.icon');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.parentElement.remove();
        });
    });

    if (window.scrollY === 0) {
        setTimeout(clean, 3000);
        return;
    }

    container.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });

    setTimeout(() => {
        const offset = 50;
        const elementPosition = container.getBoundingClientRect().top - window.scrollY;
        window.scrollTo({
            top: elementPosition - offset,
            behavior: 'smooth'
        });
    }, 100);

    // Attendre 3 secondes avant de nettoyer les alertes
    setTimeout(clean, 3000);
};
