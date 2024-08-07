
// Requête de recherche
const submitForm = (form) => {
    console.log(form);
    // on récupère les données du formumlaire et on crée un nouveau form
    const formData = new FormData(form);
    // on convertit le formualire en objet JSON
    const jsonData = Object.fromEntries(formData.entries());
    console.log(jsonData);
    const term = jsonData['search_form[search_term]']

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
        .then(response => {
            // Vérification du statut de la réponse
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Conversion de la réponse en JSON
        })
        .then(data => {
            console.log(data);
            displayResults(data, term);
            $('#search-results-container').slideDown();

        })
        .catch(error => {
            $('#search-results-container').slideDown();
            document.getElementById('search-results').innerHTML = '<p class="error">An error occurred: ' + error.message + '</p>';
        });
}
/**
 * Affichage dynamique des résultats de recherches /templates/search/index.html.twig
 * Gestion du formulaire .assets/js/formsViewSearch.js
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log('=> searchMotor.js loaded');
    // Gestion Active Link sur le moteur de recherche 
    // Facultatif prévus pour fonctionner sur plusieurs moteurs ( input )
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











    // Ajout d'un écouteur d'événement sur les radio buttons pour filtrer par prix
    const priceFilters = document.querySelectorAll('input[name="price_filter"]');
    priceFilters.forEach(radio => {
        radio.addEventListener('change', () => {
            // Récupérer le formulaire parent
            const form = search.closest('form');
            submitForm(form);
        });
    });

    let close = document.getElementById('close-results')
    if (close) {
        // Ajoutez un écouteur d'événements pour le bouton de fermeture
        document.getElementById('close-results').addEventListener('click', () => {
            $('#search-results-container').slideUp();
        });
    }


});
