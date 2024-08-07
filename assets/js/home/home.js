import React, { useState, useEffect } from 'react';
import { displayResults } from '../search/users/displayResults.js';
import { showAlert, clean } from './../alert/messageFlash.js';
import { postData } from './../ajax/postData.js';
import { createRoot } from 'react-dom/client';
import { HomePage } from './../../js/components/HomePage/HomePage'

document.addEventListener('DOMContentLoaded', function () {
    console.log("=> home.js loaded");
    function handleSearchUser(event, element) {
        event.preventDefault();
        const form = element.closest('form');
        // Récupérer les données du formulaire
        const formData = new FormData(form);

        // Ajouter un nouveau champ au formData
        // formData.append('newField', 'newValue');

        for (const [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        const searchByName = formData.get('search-user-by-name');
        // const termMobile = formData.get('search_form[search_term_mobile]');
        const term = searchByName // || termMobile;

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
                // Remonter le scroll tout en haut de la page
                window.scrollTo({ top: 0, behavior: 'smooth' });
                // abaisser les résultats de recherche
                setTimeout(() => {
                    $('#search-results-container').slideDown();
                    document.getElementById('search-results').innerHTML = resultsHtml;
                }, 400);
            }
        });
    }

    const homePage = document.getElementById("homepage-root");
    if (homePage) {
        createRoot(homePage).render(<HomePage />);
    }

    // On sélectionne toutes les icônes de recherche utilisateur du home
    const searchIcons = document.querySelectorAll('.ui.icon.input .search.icon.search-user');
    // ajouter un écouteur d'événement à chaque icône de recherche
    searchIcons.forEach((searchIcon) => {
        searchIcon.addEventListener('click', function (event) {
            handleSearchUser(event, searchIcon);
        });
    });
});