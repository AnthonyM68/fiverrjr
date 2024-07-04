

const submitForm = (formElement) => {
    // Initialiser le modal
    $('.ui.modal.navbar').modal({
        transition: 'slide down'
    });
    const formData = new FormData(formElement);
    const jsonData = Object.fromEntries(formData.entries());
    console.log(jsonData);
    console.log(formElement.action);
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
            console.log(data);
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
            $('.ui.modal.navbar').modal('show');
            document.getElementById('search-results-navbar').innerHTML = resultsHtml;
        })
        .catch(error => {
            $('.ui.modal.navbar').modal('show');
            document.getElementById('search-results-navbar').innerHTML = '<p class="error">An error occurred: ' + error.message + '</p>';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('-> ViewNavbar.js loaded');
    const searchIcon = document.getElementById('search-icon');
    const form = document.querySelector('.ajax-form');


    searchIcon.addEventListener('click', function () {
        event.preventDefault();
        submitForm(form);
    });
    form.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            console.log('enter');
            event.preventDefault();
            submitForm(form);
        }
    });
});