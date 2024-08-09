import { displayResults } from './../search/services/displayResults.js';
import { showAlert } from './../alert/messageFlash.js';
import { usePostData } from './../ajax/postData.js';

document.addEventListener('DOMContentLoaded', () => {
    console.log('=> searchMotor.js loaded');

    const formElement = document.querySelector('.ui-button.ui-widget');

    if (formElement) {
        formElement.addEventListener('click', async function (event) {
            const form = event.target.closest('.ajax-search-form');

            if (form) {
                const termInput = form.querySelector('input[name="search_term"]');
                if (termInput) {
                    const term = termInput.value;
                    if (!term) {
                        showAlert('warning', "Vous n'avez pas indiqué de mot clé de recherche");
                        event.preventDefault();
                        return;
                    }

                    const formData = new FormData(form);
                    const csrfToken = formData.get('_token');

                    if (!csrfToken) {
                        console.warn('CSRF token is missing');
                        showAlert('negative', "Token CSRF manquant");
                        event.preventDefault();
                        return;
                    }
                    try {
                        const response = await usePostData(form.action, formData, csrfToken, true);
                        console.log('Données reçues:', data);
                        // Extraire le tableau `data` de la réponse
                        const data = response.data;
                        if (Array.isArray(data)) {
                            // console.log('Les données sont bien un tableau.');
                            data.forEach((entityTheme, index) => {
                                // console.log(`Élément ${index}:`, entityTheme);
                                // if (typeof entityTheme !== 'object' || entityTheme === null) {
                                //     console.log(`Erreur: L'élément ${index} n'est pas un objet.`);
                                // }
                                const resultsHtml = displayResults(data, term);
                                // console.log(resultsHtml);
                                $('#search-results-container').slideDown();
                                document.getElementById('search-results').innerHTML = resultsHtml;
                            });
                        } else {
                            console.log('Les données ne sont pas un tableau:', typeof data);
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                    }
                } else {
                    console.warn('search_term input not found in the form');
                }
            } else {
                console.warn('Form not found');
            }
            event.preventDefault();
        });
    }
    // Ajout d'un écouteur d'événement sur les radio buttons pour filtrer par prix
    const priceFilters = document.querySelectorAll('input[name="price_filter"]');
    priceFilters.forEach(radio => {
        radio.addEventListener('change', () => {
            submitForm(formElement);
        });
    });
    // Ajoutez un écouteur d'événements pour le bouton de fermeture
    document.getElementById('close-results').addEventListener('click', () => {
        $('#search-results-container').slideUp();
    });

});
