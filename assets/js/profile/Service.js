import { showAlert } from './../alert/messageFlash.js';
import { usePostData } from './../ajax/postData.js';

// initialise les champs select du formulaire service_theme_category_course_type
const initializeForm = () => {
    const serviceItemCourseTheme = document.getElementById('service_item_course_theme');
    const serviceItemCourseCategory = document.getElementById('service_item_course_category');
    const serviceItemCourseCourse = document.getElementById('service_item_course_course');

    // récupère les données via ajax
    const fetchData = async (url) => {
        try {
            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            console.error('erreur lors de la récupération des données:', error);
            return [];
        }
    };

    // met à jour les catégories en fonction du thème sélectionné
    const updateCategories = async (e) => {
        const themeId = e.target.value;
        const categories = await fetchData(`/categories_by_theme/${themeId}`);

        serviceItemCourseCategory.innerHTML = '';
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            serviceItemCourseCategory.appendChild(option);
        });
        updateCourses();
    };

    // met à jour les cours en fonction de la catégorie sélectionnée
    const updateCourses = async () => {
        const categoryId = serviceItemCourseCategory.value;
        const courses = await fetchData(`/courses_by_category/${categoryId}`);

        serviceItemCourseCourse.innerHTML = '';
        courses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.id;
            option.textContent = course.name;
            serviceItemCourseCourse.appendChild(option);
        });
    };

    // attache les événements de changement
    if (serviceItemCourseTheme) {
        serviceItemCourseTheme.addEventListener('change', updateCategories);
    }
    if (serviceItemCourseCategory) {
        serviceItemCourseCategory.addEventListener('change', updateCourses);
    }
};
const resetFieldErrors = () => {
    const formFields = document.querySelectorAll('#service_form_ajax .field'); 
    formFields.forEach(field => {
        field.addEventListener('input', () => {
            field.classList.remove('field-error'); 
        });
    });
};
// écoute les événements du formulaire pour l'envoi ajax
const eventListnerFormService = async () => {
    const form = document.getElementById('service_form_ajax');

    if (!form) return;
    // on annule tout autre écouteur sur cet élément
    form.removeEventListener("submit", handleFormSubmit);
    // on en crée un nouveau
    form.addEventListener("submit", handleFormSubmit);
    async function handleFormSubmit(event) {
        event.preventDefault();
        event.stopPropagation();
        const formData = new FormData(form);
        const serviceId = form.getAttribute('data-service-id');
        if (serviceId) formData.append('service_id', serviceId);
        // Display the key/value pairs
        /*for (const pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }*/
        try {
            const { data, error } = await usePostData(form.action, formData, false, false);

            if (error) {
                showAlert('negative', error.message); 

                if (error.formHtml) {
                    document.getElementById('service-form-new').innerHTML = error.formHtml;
                    initializeForm();
                    eventListnerFormService();
                    resetFieldErrors(); // Réinitialiser les erreurs sur les nouveaux champs générés

                    // Masquer le loader si nécessaire
                    $('#loader-new').hide();
                    $('#loader-edit').hide();
                }
            } else {
                showAlert('positive', data.message); 
            }
        } catch (error) {
            showAlert('negative', 'Erreur lors de l\'envoi du formulaire : ' + error.message);
            console.error('erreur lors de l\'envoi du formulaire:', error);
        }
    }
};

// gestion de l'affichage du formulaire "nouveau service"
const NewServiceManager = (function () {
    const serviceFormContainer = $('#service-form');
    const serviceListContainer = $('#service-item-list');
    const editServiceContainer = $('#edit-item-service');
    const toggleButton = $('.toggle-new-service');
    const loader = $('#loader-new');

    const toggleServiceForm = async () => {
        console.log('toggle-new-service');
        // vérifie si le formulaire est visible et le ferme si nécessaire
        if (serviceFormContainer.is(':visible')) {
            serviceFormContainer.slideUp(400, () => toggleButton.text('nouveau service'));
            return;
        }

        // ferme le conteneur d'édition si visible
        if (editServiceContainer.is(':visible')) {
            editServiceContainer.slideUp(400);
        }

        // affiche le formulaire de création via ajax
        serviceFormContainer.slideDown(400, async () => {
            toggleButton.text('fermer le formulaire');
            loader.show();

            const token = toggleButton.attr('data-token');
            const formData = new FormData();
            formData.append('_token', token);

            try {
                const response = await usePostData(`/fetch/service/form/generate`, formData, false, false);
                if (response.error) {
                    showAlert('negative', response.error);
                    console.error(response.error);
                    return;
                }
                const data = response.data;
                if (data.formHtml) {
                    document.getElementById('service-form-new').innerHTML = data.formHtml;
                    initializeForm();
                    eventListnerFormService();
                    loader.hide();
                }
            } catch (error) {
                showAlert('negative', 'Erreur lors de la requête AJAX : ' + error.message);
                console.error('erreur lors de la requête ajax:', error);
            }
        });
        // ferme la liste des services si visible
        if (serviceListContainer.is(':visible')) {
            serviceListContainer.slideUp(400, () => $('.toggle-list-services').text('liste de mes services'));
        }
    };

    const initEventListeners = function () {
        toggleButton.on('click', toggleServiceForm);
    };

    return { init: initEventListeners };
})();


// gestion de l'affichage de la liste des services
const ServiceToggleManager = (function () {
    const serviceListContainer = $('#service-item-list');
    const serviceEditContainer = $('#edit-item-service');
    const serviceFormContainer = $('#service-form');
    const toggleListButton = $('.toggle-list-services');
    const loader = $('#loader-list');
    const closeEditButton = $('#close-edit'); // bouton pour fermer le conteneur d'édition

    // fonction pour fermer tous les conteneurs
    const closeAllContainers = () => {
        if (serviceListContainer.is(':visible')) {
            serviceListContainer.slideUp(400);
        }
        if (serviceFormContainer.is(':visible')) {
            serviceFormContainer.slideUp(400);
        }
        if (serviceEditContainer.is(':visible')) {
            serviceEditContainer.slideUp(400);
        }
    };

    // fonction pour afficher ou cacher la liste des services
    const toggleServiceList = () => {
        // ferme tous les conteneurs ouverts avant de gérer la liste des services
        closeAllContainers();

        // vérifie si la liste des services est visible
        if (serviceListContainer.is(':visible')) {
            // si visible, on ferme la liste et met à jour le texte du bouton
            serviceListContainer.slideUp(400, () => toggleListButton.text('liste de mes services'));
        } else {
            // si non visible, on affiche la liste et met à jour le texte du bouton
            loader.show();
            serviceListContainer.slideDown(400, () => {
                ServiceListManager.updateListeServices();
                toggleListButton.text('fermer la liste');
            });
        }
    };

    // fonction pour fermer le conteneur d'édition
    const closeEditContainer = () => {
        if (serviceEditContainer.is(':visible')) {
            serviceEditContainer.slideUp(400);
        }
    };

    // initialisation des écouteurs d'événements
    const initEventListeners = () => {
        // écouteur pour le bouton de la liste des services
        toggleListButton.on('click', toggleServiceList);

        // écouteur pour le bouton de fermeture de l'édition
        closeEditButton.on('click', closeEditContainer);
    };

    return { init: initEventListeners };
})();



// gestion de la liste des services et des actions éditer/supprimer
export const ServiceListManager = (function () {
    // container
    const serviceItemList = $('#service-item-list');
    // table
    const tableBody = document.getElementById("body-table-list");
    // met à jour la liste des services
    const updateListeServices = async () => {
        console.log('updateListeServices');
        try {
            const response = await fetch('/fetch/get_service');
            const data = await response.json();

            tableBody.innerHTML = '';
            data.forEach(service => {
                const tr = document.createElement('tr');

                // colonne titre du service
                const tdTitle = document.createElement('td');
                tdTitle.setAttribute('data-label', 'service-title');
                tdTitle.textContent = service.title;
                tr.appendChild(tdTitle);

                // colonne boutons (éditer/supprimer)
                const tdButtons = document.createElement('td');
                tdButtons.setAttribute('data-label', 'service-btn');
                const buttonGroup = document.createElement('div');
                buttonGroup.className = 'ui buttons';

                // bouton éditer
                const editButton = document.createElement('a');
                editButton.className = 'ui-button ui-widget ui-corner-all toggle-edit-service';
                editButton.innerHTML = '<span class="ui-icon ui-icon-pencil"></span>';
                editButton.setAttribute('href', "javascript:void(0);");
                editButton.setAttribute('data-service-id', service.id);
                editButton.setAttribute(`data-token-${service.id}`, service.csrf_token);
                editButton.setAttribute('title', 'éditer service');
                buttonGroup.appendChild(editButton);

                // bouton supprimer
                const deleteButton = document.createElement('a');
                deleteButton.className = 'ui-button ui-widget ui-corner-all toggle-trash-service';
                deleteButton.innerHTML = '<span class="ui-icon ui-icon-trash"></span>';
                deleteButton.setAttribute('href', "javascript:void(0);");
                deleteButton.setAttribute('data-service-id', service.id);
                deleteButton.setAttribute(`data-token-${service.id}`, service.csrf_token);
                deleteButton.setAttribute('title', 'supprimer service');
                buttonGroup.appendChild(deleteButton);

                tdButtons.appendChild(buttonGroup);
                tr.appendChild(tdButtons);
                tableBody.appendChild(tr);
            });
            $('#loader-list').hide();

        } catch (error) {
            console.error('erreur lors de la récupération des services:', error);
        }
    };

    // gère les clics sur les boutons éditer/supprimer
    const handleListClick = async (event) => {
        const target = event.target.closest('.ui-button');
        if (!target) return;

        // gestion de l'édition
        if (target.classList.contains('toggle-edit-service')) {
            $('#loader-edit').show();
            console.log('toggle-edit-service');

            const serviceId = target.getAttribute('data-service-id');
            const csrfToken = target.getAttribute(`data-token-${serviceId}`);
            const formData = new FormData();
            formData.append('service_id', serviceId);
            formData.append('_token', csrfToken);
            // debug formdata
            for (const pair of formData.entries()) {
                console.log(pair[0], pair[1]);
            }
            try {
                // On génére le service à éditer
                const response = await usePostData('/fetch/service/form/generate', formData, false, false);
                const data = response.data;

                if (data.formHtml) {
                    $('#edit-item-service').slideDown(400);
                    document.getElementById('service-form-edit').innerHTML = data.formHtml;
                    eventListnerFormService();
                    $('#loader-edit').hide();
                } else if (data.message) {
                    showAlert('positive', data.message);
                } else if (data.errors) {
                    showAlert('negative', 'Une erreur est survenue lors de la récupération du formulaire.');
                }

                if ($('#service-item-list').is(':visible')) {
                    $('#service-item-list').slideUp(400, () => $('.toggle-list-services').text('liste de mes services'));
                }
            } catch (error) {
                showAlert('negative', 'Erreur lors de l\'édition du service : ' + error.message);
                console.error('erreur lors de l\'édition du service:', error);
            }

            // gestion de la suppression
        } else if (target.classList.contains('toggle-trash-service')) {
            const serviceId = target.getAttribute('data-service-id');
            const csrfToken = target.getAttribute(`data-token-${serviceId}`);

            if (confirm('confirmez-vous la suppression du service?')) {
                try {
                    const response = await fetch(`/fetch/service/delete`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ serviceId, _token: csrfToken })
                    });

                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.error || 'erreur lors de la suppression');
                    }

                    target.closest('tr').remove();
                    showAlert('positive', 'Service supprimé avec succès !'); // ajout de l'alerte de succès
                } catch (error) {
                    showAlert('negative', 'Erreur lors de la suppression du service : ' + error.message); // ajout de l'alerte d'erreur
                    console.error('erreur lors de la suppression du service:', error);
                }
            }
        }
    };

    const initEventListeners = () => {
        if (serviceItemList.length) {
            serviceItemList.on('click', handleListClick);
        }
    };

    return {
        init: initEventListeners,
        updateListeServices: updateListeServices
    };
})();




// initialisation des modules au chargement de la page
document.addEventListener('DOMContentLoaded', () => {

    initializeForm();
    NewServiceManager.init();
    ServiceToggleManager.init();
    ServiceListManager.init();
});
