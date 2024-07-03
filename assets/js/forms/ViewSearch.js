/**
 * Affichage dynamique des résultats de recherches /templates/search/index.html.twig
 * Formulaire SearchFormType
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialise le modal
    $('.ui.modal').modal('show');

    console.log('DOM fully loaded and parsed: ViewSearch.js');
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


    // Intercepter la soumission du formulaire (Service-search-motor ou Them-search-motor) SearchController
    const forms = document.querySelectorAll('.ajax-form');

    forms.forEach(form => {
        form.addEventListener('submit', function (event) {

            event.preventDefault(); // Empêche le rechargement de la page
            // Récupère les données du formulaire
            const formData = new FormData(this);
            console.log(formData);
            let actionUrl = this.getAttribute('action');
            console.log(actionUrl);

            if (actionUrl == null) {
                actionUrl = 'search/results';
            }
            // Récupère le type de formulaire soumis
            const submittedFormType = formData.get('submitted_form_type');

            console.log(submittedFormType);
            // Envoie les données du formulaire via Fetch API
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token("search_courses") }}'
                },
                body: formData,
            })
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    console.log(data.submitted_form);
                    
                    if (data.error) {
                        document.getElementById('search-results').innerHTML = '<p class="error">An error occurred: ' + data.error + '</p>';
                        return;
                    }

                    let resultsHtml = '';

                    if (data.results.empty) {
                        resultsHtml += '<h2>Aucun résultats</h2>';
                        document.getElementById('search-results').innerHTML = resultsHtml;
                        // Si pas de réultats on quitte
                        return;
                    }
                    // Mise à jour du contenu avec les résultats
                    if (data.submitted_form === 'service' && data.results.service) {

                        resultsHtml += '<h3>Résultats pour Service</h3><div class="ui divided items">';
                        data.results.service.forEach(service => {
                            resultsHtml += `
               <div class="item">
                 <div class="image">
                   <img src="./img/${service.picture}">
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

                    if (data.submitted_form === 'test2' && data.results.course) {

                        resultsHtml += '<h2>Résultats par Sous-catégorie</h2><ul>';
                        data.results.course.forEach(course => {
                            console.log(course.nameCourse.includes(searchTerm));
                            if (course.nameCourse.includes(searchTerm)) {
                                resultsHtml += `<li>${course.nameCourse}</li>`;
                            }
                        });
                        resultsHtml += '</ul>';
                    }
                    document.getElementById('search-results').innerHTML = resultsHtml;
                })
                .catch(error => {
                    document.getElementById('search-results').innerHTML = '<p class="error">An error occurred: ' + error.message + '</p>';
                });
        });
    });
});
