/**
 * Affichage dynamique des résultats de recherches /templates/search/index.html.twig
 * Formulaire SearchFormType
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed: ViewSearch.js');
    $('.ui.modal').modal('show');
    // Intercepter la soumission du formulaire (Service-search-motor ou Them-search-motor) SearchController
    const forms = document.querySelectorAll('.ajax-form');
    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            console.log('Form submitted');
            event.preventDefault(); // Empêche le rechargement de la page
            // Récupère les données du formulaire
            const formData = new FormData(this);
            const actionUrl = this.getAttribute('action');
            // Récupère le type de formulaire soumis
            const submittedFormType = formData.get('submitted_form_type');
            console.log('Form submitted:', submittedFormType);
            // Envoie les données du formulaire via Fetch API
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
            })
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    console.log(data);
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

                    if (data.submitted_form === 'form_theme' && data.results.theme) {
                        resultsHtml += '<h2>Résultats pour Thème</h2><ul>';
                        data.results.theme.forEach(theme => {
                            resultsHtml += `<li>${theme.nameTheme}</li>`;
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
