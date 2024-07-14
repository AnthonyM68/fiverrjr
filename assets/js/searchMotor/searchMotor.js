const displayResults = (results, searchTerm) => {

    // $('.ui.modal.navbar').modal({
    //     transition: 'slide down'
    // }).modal('show');

    let resultsHtml = '';

    if (!results || results.length === 0) {
        resultsHtml = '<h2>Aucun résultat</h2>';
    } else {
        resultsHtml += '<h3>Résultats</h3>';
        const searchTermLower = searchTerm.toLowerCase();

        results.forEach(entityTheme => {
            if (entityTheme && entityTheme.nameTheme) {
                resultsHtml += `<div class="item">
                <i class="folder open outline icon"></i>
                <div class="content">
                <div class="header"><strong>Thème : ${entityTheme.nameTheme}</strong></div>
                </div>
                </div>`;

                entityTheme.categories.forEach(category => {
                    resultsHtml += `<div class="item">
                        <i class="folder open outline icon"></i>
                        <div class="content">
                        <div class="header"><strong>Catégorie : ${category.nameCategory}</strong></div>
                        </div>
                        </div>`;

                    category.courses.forEach(course => {
                        resultsHtml += `<div class="item">
                        <i class="folder open outline icon"></i>
                        <div class="content">
                        <div class="header"><strong>Sous-catégorie : ${course.nameCourse}</strong></div>
                        </div>
                        </div>`;

                        course.serviceItems.forEach(serviceItem => {
                            if (serviceItem.title.toLowerCase().includes(searchTermLower) ||
                                serviceItem.description.toLowerCase().includes(searchTermLower)) {

                                resultsHtml += `<div class="item">
                                <i class="file icon"></i>
                                <div class="content">
                                <div class="header"><a href="/detail_service/${serviceItem.id}">${serviceItem.title}</a></div>
                                <div class="description">${serviceItem.description}</div>
                                </div>
                                </div>`;
                            }
                        });
                    });
                });
            }
        });
    }
    document.getElementById('search-results').innerHTML = resultsHtml;
};

// Requête de recherche
const submitForm = (formElement) => {
    // on récupère les données du formumlaire et on crée un nouveau form
    const formData = new FormData(formElement);
    console.log(formData);
    // on convertit le formualire en objet JSON
    const jsonData = Object.fromEntries(formData.entries());

    const term = jsonData['search_form[search_term]']

    console.log(jsonData);

    fetch(formElement.action, {
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
            console.log(term);
            displayResults(data, term);
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
    console.log('=> searchMotor.js loaded');
    // Initialise le modal

    // Gestion Active Link sur les moteur de recherche
    // Sélectionnez tous les éléments de menu (les moteur de recherches, les formulaires)
    // const menuItems = document.querySelectorAll('.ui.vertical.fluid.menu .item.field');
    // Ajoutez un gestionnaire de clic à chaque élément de menu
    // menuItems.forEach(item => {
    //     item.addEventListener('click', function () {
    //         // Supprimez la classe 'active teal' de tous les éléments de menu
    //         menuItems.forEach(menu => menu.classList.remove('active', 'teal'));
    //         // Ajoutez la classe 'active teal' à l'élément cliqué
    //         this.classList.add('active', 'teal');
    //     });
    // });


    // On sélectionne le formulaire de recherche utilisé pour envois par AJAX
    const search = document.querySelector('.search.link.icon');
    // Intercepter la soumission du formulaire (Service-search-motor ou Theme-search-motor)
    search.addEventListener('click', function (event) {
        event.preventDefault();
        // Récupérer le formulaire parent
        const form = search.closest('form');
        submitForm(form);
    });

    // Ajout d'un écouteur d'événement sur les radio buttons pour filtrer par prix
    const priceFilters = document.querySelectorAll('input[name="price_filter"]');
    priceFilters.forEach(radio => {
        radio.addEventListener('change', () => {
            submitForm(formElement);
        });
    });
});
