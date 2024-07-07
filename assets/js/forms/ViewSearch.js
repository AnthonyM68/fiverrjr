(function(){
    // Gestion Active Link sur les moteur de recherche
    // Sélectionnez tous les éléments de menu (les moteur de recherches, les formulaires)
    const menuItems = document.querySelectorAll('.ui.vertical.fluid.menu .item.field');
    // Ajoutez un gestionnaire de clic à chaque élément de menu
    menuItems.forEach(item => {
        item.addEventListener('click', function () {
            // Supprimez la classe 'active teal' de tous les éléments de menu
            menuItems.forEach(menu => menu.classList.remove('active', 'teal'));
            // Ajoutez la classe 'active teal' à l'élément cliqué
            this.classList.add('active', 'teal');
        });
    });
}());

const submitForm = (formElement) => {
    const formData = new FormData(formElement);
    const jsonData = Object.fromEntries(formData.entries());
    fetch(formElement.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
        .then(response => {
            return response.json();
        })
        .then(data => {
            let resultsHtml = '';
            if (data.error) {
                document.getElementById('search-results').innerHTML = '<p class="error">An error occurred: ' + data.error + '</p>';
                // On affiche le feedback et on quitte
                return;
            }
            if (data.service.length === 0) {
                resultsHtml += '<h2>Aucun résultats</h2>';
                document.getElementById('search-results').innerHTML = resultsHtml;
                // Si pas de réultats on quitte
                return;
            }
            // Mise à jour du contenu avec les résultats
            if (data.submitted_form === 'service' && data.service.length !== 0) {

                resultsHtml += '<h3>Résultats pour Service </h3><div class="ui divided items">';
                data.service.forEach(service => {
                    resultsHtml += `
       <div class="item">
         <div class="image">
           <img src="${service.picture}">
         </div>
         <div class="content">
           <a class="header">${service.title}</a>
           <div class="meta">

           </div>
           <div class="description">
             <p>${service.description}</p>
           </div>

         </div>
       </div>`;
                });
                resultsHtml += '</div>';
            }
            document.getElementById('search-results').innerHTML = resultsHtml;
        })
        .catch(error => {
            document.getElementById('search-results').innerHTML = '<p class="error">An error occurred: ' + error.message + '</p>';
        });
}
/**
 * Affichage dynamique des résultats de recherches /templates/search/index.html.twig
 * Gestion du formulaire .assets/js/formsViewSearch.js
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('-> ViewSearch.js loaded');
    // Initialise le modal
    $('.ui.modal.search').modal('show');

    // On sélectionne le formulaire de recherche utilisé pour envois par AJAX
    const formElement = document.querySelector('.ajax-search-form');
     // Intercepter la soumission du formulaire (Service-search-motor ou Theme-search-motor)
     formElement.addEventListener('submit', function (event) {
        event.preventDefault();
        submitForm(formElement);
    });

    // Ajout d'un écouteur d'événement sur les radio buttons pour filtrer par prix
    const priceFilters = document.querySelectorAll('input[name="price_filter"]');
    priceFilters.forEach(radio => {
        radio.addEventListener('change', () => {
            submitForm(formElement);
        });
    });
});
