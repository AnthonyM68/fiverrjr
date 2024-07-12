const displayResults = (results, searchTerm) => {
    let resultsHtml = '';

    if (!results || results.length === 0) {
        resultsHtml = '<h2>Aucun résultat</h2>';
    } else {
        resultsHtml += '<h3>Résultats</h3><ul>';
        
        console.log(Array.isArray(results))

        results.forEach(result => {
            console.log(result);
            // let displayTheme = false;
            // let themeHtml = `<li><strong>Thème : ${result.nameTheme}</strong><ul>`;
            
            // result.categories.forEach(category => {
            //     let displayCategory = false;
            //     let categoryHtml = `<li><strong>Catégorie : ${category.nameCategory}</strong><ul>`;
                
            //     category.courses.forEach(course => {
            //         let displayCourse = false;
            //         let courseHtml = `<li><strong>Cours : ${course.nameCourse}</strong><ul>`;
                    
            //         course.serviceItems.forEach(serviceItem => {
            //             if (serviceItem.title.toLowerCase().includes(searchTerm) || 
            //                 serviceItem.description.toLowerCase().includes(searchTerm)) {
            //                 displayTheme = true;
            //                 displayCategory = true;
            //                 displayCourse = true;
            //                 courseHtml += `
            //                     <li>
            //                         <a href="/detail_service/${serviceItem.id}">${serviceItem.title}</a>
            //                     </li>
            //                     <li>
            //                         <a href="/detail_service/${serviceItem.id}">${serviceItem.description}</a>
            //                     </li>`;
            //             }
            //         });
                    
            //         if (displayCourse) {
            //             courseHtml += '</ul></li>';
            //             categoryHtml += courseHtml;
            //         }
            //     });
                
            //     if (displayCategory) {
            //         categoryHtml += '</ul></li>';
            //         themeHtml += categoryHtml;
            //     }
            // });
            
            if (displayTheme) {
                themeHtml += '</ul></li>';
                resultsHtml += themeHtml;
            }
        });
        
        resultsHtml += '</ul>';
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

            displayResults(data, jsonData.search_term.toLowerCase());  
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
    $('.ui.modal.search').modal({
        transition: 'slide down'
    }).modal('show');
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


    // On sélectionne le formulaire de recherche utilisé pour envois par AJAX
    const formElement = document.querySelector('.ajax-search-form');
    // Intercepter la soumission du formulaire (Service-search-motor ou Theme-search-motor)
    formElement.addEventListener('submit', function (event) {
        event.preventDefault();
        submitForm(formElement);
    });

    // Ajout d'un écouteur d'événement sur les radio buttons pour filtrer par prix
    /*const priceFilters = document.querySelectorAll('input[name="price_filter"]');
    priceFilters.forEach(radio => {
        radio.addEventListener('change', () => {
            submitForm(formElement);
        });
    });*/
});
