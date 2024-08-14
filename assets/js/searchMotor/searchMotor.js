import { displayResults } from './../search/services/displayResults.js';
import { showAlert } from './../alert/messageFlash.js';
import { usePostData } from './../ajax/postData.js';

document.addEventListener('DOMContentLoaded', () => {
    console.log('=> searchMotor.js loaded');
    // on recherche l'élement du DOM que l'on souhaite écouter
    const formElement = document.querySelector('.ui.button.widget.primary');
    // // si l'élement est présent
    if (formElement) {
        // on ajoute un écouteur dévénement
        formElement.addEventListener('click', async function (event) {
            event.preventDefault();  // Empêche le comportement par défaut de soumission du formulaire
            event.stopPropagation(); // Empêche d'autres événements de se propager
            // si l'événement attendu par click a lieu
            // on recherche l'élement parent .ajax-search-form' auquel il apartient
            const form = event.target.closest('.ajax-search-form');
            // s'il est présent
            if (form) {
                // on recherche l'élément Node du DOM de type input et name="search_term" devant contenir le terme rechercher 
                const termInput = form.querySelector('input[name="search_term"]');
                // si le term existe
                if (termInput) {
                    // on recherche sa valeur 
                    const term = termInput.value;
                    // si le term n'existe pas on déclenche une alerte javascript personnalisé et quittons
                    if (!term) {
                        showAlert('warning', "Vous n'avez pas indiqué de mot clé de recherche");
                        return;
                    }
                    // on créer un nouveau formdata avec les données de notre formulaire
                    const formData = new FormData(form);

                    formData.forEach((value, key) => {
                        console.log(`{${key} : ${value}}`); 
                    });
                    // depuis ce formulaire on recherche la valeur du champ caché "_token"
                    // et on sauvegarde
                    const csrfToken = formData.get('_token');
                    // si le token n'existe pas on déclenche une alerte javascript et quittons
                    if (!csrfToken) {
                        showAlert('negative', "Token CSRF manquant");
                        return;
                    }
                    try {
                        // on effectue la requête AJAX via fetch()
                        // on utilise la fonction usePostData, une requête personnalisée
                        // pouvant prendre en argument un csrfToken et un booléen indiquant s'il est nécessaire de convertir en JSON
                        const response = await usePostData(form.action, formData, csrfToken, true);
                        console.log('Raw response object:', response);
                        // Extraire le tableau `data` de la réponse
                        const data = response.data;
                        console.log('Données reçues:', data);

                        // Vérifier si data est bien un tableau
                        if (Array.isArray(data)) {
                            // Appeler la fonction js displayResults
                            // Envoyer à la fonction les data (tableau de résultats)
                            const resultsHtml = displayResults(data, term);

                            // $('#search-results-container').slideDown();
                            // document.getElementById('search-results').innerHTML = resultsHtml;

                            $('#search-results').html(resultsHtml);

                            $('#search-results-container').slideDown();


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
        });
    }
    // Ajout d'un écouteur d'événement sur les radio buttons pour filtrer par prix
    const priceFilters = document.querySelectorAll('input[name="price_filter"]');
    if (priceFilters) {
        priceFilters.forEach(radio => {
            radio.addEventListener('change', () => {
                submitForm(formElement);
            });
        });
    }
    // Ajoutez un écouteur d'événements pour le bouton de fermeture
    const close = document.getElementById('close-results');
    if (close) {
        close.addEventListener('click', () => {
            $('#search-results-container').slideUp();
        });
    }


});
