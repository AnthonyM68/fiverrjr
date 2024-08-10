import { displayResults } from '../search/services/displayResults.js';
import { showAlert, clean } from './../alert/messageFlash.js';
import { usePostData } from './../ajax/postData.js';

document.addEventListener('DOMContentLoaded', async () => {

    console.log('=> navbar.js loaded');
    // Toggle button burger
    $('.ui.basic.icon.toggle.button').on('click', function () {
        $('.ui.accordion.vertical.menu').toggle("250", "linear");
    });

    // Initialize accordion for mobile
    $('.ui.accordion').accordion();
    // gestion des lien active de la navigation
    const links = document.querySelectorAll('.computer.only .item');
    links.forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
        link.addEventListener('click', function () {
            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Gestion du menu mobile
    const mobileMenu = document.querySelector('.tablet.mobile.only .ui.vertical.menu');

    const mobileMenuToggle = document.querySelector('#menu-burger');

    // // Ferme le menu mobile en cliquant en dehors
    document.addEventListener('click', function (event) {
        console.log(mobileMenu.contains(event.target), mobileMenuToggle.contains(event.target));
        // Vérifie si le clic est en dehors du menu mobile et du bouton de toggle
        if (!mobileMenu.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
            $('.ui.vertical.menu').toggle("250", "linear");

        }
    });

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
        // console.log('Term:', term);
        // Envoyer les données avec postData et gérer la réponse
        usePostData(form.action, formData).then(({ data, error }) => {
            if (error) {
                console.error('Error during fetch:', error);
                showAlert('negative', 'Une erreur est survenue lors de la recherche.');
            } else {
                console.log('Données reçues:', data);
                const resultsHtml = displayResults(data, term);
                console.log(resultsHtml);
                $('#search-results-container').slideDown();
                document.getElementById('search-results').innerHTML = resultsHtml;
            }
        });
    }


    // On sélectionne toutes les icônes de recherche de la navbar
    const searchIcons = document.querySelectorAll('.ui.icon.input .search.icon.search-service');
    // ajouter un écouteur d'événement à chaque icône de recherche
    searchIcons.forEach((searchIcon) => {
        searchIcon.addEventListener('click', function (event) {
            handleSearch(event, searchIcon);

        });
    });
    const searchFields = document.querySelectorAll('input[name="search_form[search_term_desktop]"], input[name="search_form[search_term_mobile]"]');
    // ajouter un écouteur d'événement sur la touche enter
    searchFields.forEach((searchField) => {
        searchField.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                handleSearch(event, searchField);
            }
        });
    });

});