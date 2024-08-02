import { displayResults } from '../search/display/displayResults.js';
import { showAlert, clean } from './../alert/messageFlash.js';
import { postData } from './../ajax/postData.js';

document.addEventListener('DOMContentLoaded', async () => {


    console.log('=> navbar.js loaded');

    function handleSearch(event, element) {
        event.preventDefault();
        const form = element.closest('form');
        // console.log(form);
        // Récupérer les données du formulaire
        const formData = new FormData(form);

        const termDesktop = formData.get('search_form[search_term_desktop]');
        const termMobile = formData.get('search_form[search_term_mobile]');
        const term = termDesktop || termMobile;

        if (!term) {
            showAlert('warning', "Vous n'avez pas indiqué de mot clé de recherche");
            return;
        }

        console.log('Term:', term);

        // Envoyer les données avec postData et gérer la réponse
        postData(form.action, formData).then(({ data, error }) => {
            if (error) {
                console.error('Error during fetch:', error);
                showAlert('negative', 'Une erreur est survenue lors de la recherche.');
            } else {
                console.log('Données reçues:', data);
                const resultsHtml = displayResults(data, term);
                $('#search-results-container').slideDown();
                document.getElementById('search-results').innerHTML = resultsHtml;
                $('#search-results-container').slideDown();
            }
        });
    }


    // On sélectionne toutes les icônes de recherche de la navbar
    const searchIcons = document.querySelectorAll('.ui.icon.input .search.icon');
    // Ajouter un écouteur d'événement à chaque icône de recherche
    searchIcons.forEach((searchIcon) => {
        searchIcon.addEventListener('click', function (event) {
            handleSearch(event, searchIcon);
        });
    });
    const searchFields = document.querySelectorAll('input[name="search_form[search_term_desktop]"], input[name="search_form[search_term_mobile]"]');

    searchFields.forEach((searchField) => {
        searchField.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                handleSearch(event, searchField);
            }
        });
    });

});