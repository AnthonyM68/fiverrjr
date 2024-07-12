const displayResults = (results) => {
    $('.ui.modal.navbar').modal({
        transition: 'slide down'
    }).modal('show');

    console.log(results);

    let resultsHtml = '';

    if (!results || results.length === 0) {
        resultsHtml = '<h2>Aucun résultat</h2>';
    } else {
        resultsHtml += '<h3>Résultats</h3><ul>';
        // const searchTermLower = searchTerm.toLowerCase();

        results.forEach(entityTheme => {
            if (entityTheme && entityTheme.nameTheme) {
                resultsHtml += `<li><strong>Thème : ${entityTheme.nameTheme}</strong></li>`;
                resultsHtml += '<ul style="margin-left: 20px;">';


                entityTheme.categories.forEach(category => {
                    let displayCategory = false;
                    resultsHtml += `<li><strong>Catégorie : ${category.nameCategory}</strong></li>`;
                    resultsHtml += '<ul style="margin-left: 20px;">';
    
                    category.courses.forEach(course => {
                        let displayCourse = false;
                        resultsHtml += `<li><strong>Sous-catégorie : ${course.nameCourse}</strong></li>`;
                        resultsHtml += '<ul style="margin-left: 20px;">';
    
                        course.serviceItems.forEach(serviceItem => {
                            if (serviceItem.title.toLowerCase().includes(searchTermLower) || 
                                serviceItem.description.toLowerCase().includes(searchTermLower)) {
                                displayCategory = true;
                                displayCourse = true;
                                resultsHtml += `
                                    <li>
                                        <a href="/detail_service/${serviceItem.id}">${serviceItem.title}</a>
                                    </li>
                                    <li>
                                        <a href="/detail_service/${serviceItem.id}">${serviceItem.description}</a>
                                    </li>`;
                            }
                        });
    
                        if (!displayCourse) {
                            resultsHtml = resultsHtml.slice(0, -5); // Remove last closing </ul>
                        }
                        resultsHtml += '</ul>'; // Close sous-catégorie
                    });
    
                    if (!displayCategory) {
                        resultsHtml = resultsHtml.slice(0, -5); // Remove last closing </ul>
                    }
                    resultsHtml += '</ul>'; // Close catégorie
                });
            }

            //resultsHtml += '</ul>'; // Close thème
        });

        resultsHtml += '</ul>'; // Close résultats
    }

    document.getElementById('search-results').innerHTML = resultsHtml;
};

// Requête de recherche
const submitForm = (formElement) => {
    const formData = new FormData(formElement);
    const jsonData = Object.fromEntries(formData.entries());
    console.log(jsonData);
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

            displayResults(data);
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
