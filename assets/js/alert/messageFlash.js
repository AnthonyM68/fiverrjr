
document.addEventListener('DOMContentLoaded', () => {
    console.log('=> message_flash.js');

    // Sélectionnez toutes les alertes Semantic UI
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
