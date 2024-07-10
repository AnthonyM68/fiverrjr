const submitForm = (form) => {
    // Initialiser le modal avec l'effet slide down
    $('.ui.modal.navbar').modal({
        transition: 'slide down'
    });
    // On récupère les données du formulaire
    // et on crée une nouvelle instance de formData
    const formData = new FormData(form);
    // On convertit les données du formulaire en un objet JavaScript
    const jsonData = Object.fromEntries(formData.entries());
    // A travers le form actuel on recherche l'url de l'attribut action
    // On effectue la Requête AJAX
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        // On sérialise l'objet javascript au format JSON
        body: JSON.stringify(jsonData)
    })
        .then(response => {
            // On convertit la reponse JSON en un objet js
            return response.json();
        })
        .then(data => {
            // On initialise une chaine de caractère vide
            let resultsHtml = '';
            // S'il y'a de erreurs
            if (data.error) {
                document.getElementById('search-results').innerHTML = '<div class="ui-state-error ui-corner-all margin-bottom"><p class="error">An error occurred: ' + data.error + '</p></div>';
                // On affiche le feedback et on quitte
                return;
            }
            // S'il n'y a pas de services
            if (data.service.length === 0) {
                resultsHtml += '<h2>Aucun résultats</h2>';
                document.getElementById('search-results').innerHTML = resultsHtml;
                // Si pas de réultats on quitte
                return;
            }
            // Mise à jour du contenu avec les résultats
            if (data.submitted_form === 'service' && data.service.length !== 0) {
                // On incrémente notre chaine vide des résultat sous forme de chaine HTML
                // elements item d'une liste
                resultsHtml += '<h3>Résultats pour Service </h3><div class="ui divided items">';
                data.service.forEach(service => {
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
            // On ouvre le modal
            $('.ui.modal.navbar').modal('show');
            document.getElementById('search-results-navbar').innerHTML = resultsHtml;
        })
        .catch(error => {
            // On ouvre le modal
            $('.ui.modal.navbar').modal('show');
            document.getElementById('search-results-navbar').innerHTML = '<div class="ui-state-error ui-corner-all ui"><p>An error occurred: ' + error.message + '</p></div>';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('=> ViewNavbar.js loaded!');
    const searchIcon = document.getElementById('search-icon');
    // console.log(searchIcon);
    const form = document.querySelector('.ajax-form');
    // console.log(form);
    // On place un écouteur d'événement sur l'icon SEARCH
    searchIcon.addEventListener('click', function (event) {
        if (document.getElementById('search_form_search_term').value.trim()) {
            event.preventDefault();
            submitForm(form);
        }

    });
    // On place un ecouteur d'évenement pour la touche ENTER
    form.addEventListener('keydown', function (event) {
        if (document.getElementById('search_form_search_term').value.trim()) {
            event.preventDefault();
            submitForm(form);
        }
    });
});
