import { displayResults } from '../search/services/displayResults.js';
import { showAlert, clean } from './../alert/messageFlash.js';
import { usePostData } from './../ajax/postData.js';

document.addEventListener('DOMContentLoaded', async () => {

    console.log('=> navbar.js loaded');
    // // Toggle button burger
    // $('.ui.basic.icon.toggle.button').on('click', function () {
    //     $('.ui.accordion.vertical.menu').toggle("250", "linear");
    // });
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
    // gestion du menu mobile
    const mobileMenu = document.querySelector('.tablet.mobile.only .ui.vertical.menu');
    const mobileMenuToggle = document.querySelector('#menu-burger');
    let menuOpen = false; // Flag to track menu state

    // Fonction pour ouvrir/fermer le menu
    const toggleMobileMenu = () => {
        $(mobileMenu).toggle("250", "linear");
        menuOpen = !menuOpen; // Toggle the menuOpen flag
    };

    // Clic sur le bouton burger pour ouvrir/fermer le menu
    mobileMenuToggle.addEventListener('click', function (e) {
        e.stopPropagation(); // Prevent immediate propagation
        toggleMobileMenu();
    });

    // Fonction pour gérer le clic en dehors du menu
    document.addEventListener('click', function (e) {
        if (menuOpen && !mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
            toggleMobileMenu();
        }
    });


    function handleSearch(event, element) {

        event.preventDefault();
        const form = element.closest('form');
        // Récupérer les données du formulaire
        const formData = new FormData(form);

        // Boucle sur le FormData pour vérifier son contenu
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }


        const termDesktop = formData.get('search_form[search_term_desktop]');
        const termMobile = formData.get('search_form[search_term_mobile]');
        const term = termDesktop || termMobile;

        if (!term) {
            showAlert('warning', "Vous n'avez pas indiqué de mot clé de recherche");
            return;
        }
        const csrfToken = formData.get('search_form[_token]');
        // si le token n'existe pas on déclenche une alerte javascript et quittons
        if (!csrfToken) {
            showAlert('negative', "Token CSRF manquant");
            return;
        }
        console.log(csrfToken);
        // Envoyer les données avec postData et gérer la réponse
        usePostData(form.action, formData, false, false).then(({ data, error }) => {
            if (error) {
                console.error('Error during fetch:', error);
                showAlert('negative', 'Une erreur est survenue lors de la recherche.');
            } else {
                console.log('Données reçues de postData:', data);
                const resultsHtml = displayResults(data, term);
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