document.addEventListener('DOMContentLoaded', () => {

    console.log('=> Service.js loaded');
    const service_item_course_theme = document.getElementById('service_item_course_theme');
    const service_item_course_category = document.getElementById('service_item_course_category');
    const service_item_course_course = document.getElementById('service_item_course_course');
    const service_item_list = document.getElementById('service_item_list');
    const serviceForm = document.getElementById('service-form');

    // Récupère les données à utilisés dans les select
    const updateForm = async (url) => {
        try {
            const req = await fetch(url);
            return await req.json();
        } catch (error) {
            console.error('Failed to fetch or parse JSON:', error);
            return [];
        }
    };
    // Mets à jour par Thème, les Catégories du select  
    const updateCategories = async (e) => {
        const themeId = e.target.value;

        const url = `/categories_by_theme/${themeId}`;
        const categories = await updateForm(url);

        service_item_course_category.innerHTML = '';
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            service_item_course_category.appendChild(option);
        });
        updateCourses();
    };
    // Mets à jour les Sous-catégories du select par Catégorie
    const updateCourses = async () => {
        const categoryId = service_item_course_category.value;

        const url = `/courses_by_category/${categoryId}`;
        const courses = await updateForm(url);

        service_item_course_course.innerHTML = '';
        courses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.id;
            option.textContent = course.name;
            service_item_course_course.appendChild(option);
        });
    };

    const updateListeServices = async () => {
        const url = '/get_service_by_user';
        try {
            const req = await fetch(url);
            if (!req.ok) {
                throw new Error(`HTTP error! Status: ${req.status}`);
            }
            const contentType = req.headers.get('content-type');

            if (contentType && contentType.indexOf('application/json') !== -1) {
                const data = await req.json();
                console.log('Data all services by userId:', data);

                service_item_list.innerHTML = '';

                data.forEach(service => {
                    const tr = document.createElement('tr');

                    const tdTitle = document.createElement('td');
                    tdTitle.setAttribute('data-label', 'service-title');
                    tdTitle.textContent = service.title;
                    tr.appendChild(tdTitle);

                    const tdButtons = document.createElement('td');
                    tdButtons.setAttribute('data-label', 'service-btn');

                    const buttonGroup = document.createElement('div');
                    buttonGroup.className = 'ui buttons';

                    // Bouton edit 
                    const editButton = document.createElement('a');
                    editButton.className = 'ui-button ui-widget ui-corner-all toggle-edit-service';
                    editButton.innerHTML = '<span class="ui-icon ui-icon-pencil"></span>';
                    editButton.setAttribute('href', "javascript:void(0);");
                    editButton.setAttribute('data-service-id', service.id);// service ID
                    editButton.setAttribute(`data-token-${service.id}`, service.csrf_token); // TOken
                    editButton.setAttribute('title', 'Editer Service'); // Tooltip text
                    buttonGroup.appendChild(editButton);

                    // Bouton de suppression
                    const deleteButton = document.createElement('a');
                    deleteButton.className = 'ui-button ui-widget ui-corner-all toggle-trash-service';
                    deleteButton.innerHTML = '<span class="ui-icon ui-icon-trash"></span>';
                    deleteButton.setAttribute('href', "javascript:void(0);");
                    deleteButton.setAttribute('data-service-id', service.id);// service ID
                    deleteButton.setAttribute(`data-token-${service.id}`, service.csrf_token); // TOken
                    deleteButton.setAttribute('title', 'Supprimer Service'); // Tooltip text
                    buttonGroup.appendChild(deleteButton);
                    tdButtons.appendChild(buttonGroup);
                    tr.appendChild(tdButtons);

                    service_item_list.appendChild(tr);

                    // Ajouter un écouteur d'événement sur chaque bouton d'édition
                    document.querySelectorAll('.toggle-edit-service').forEach(button => {

                        button.addEventListener('click', async function () {
                            try {
                                const serviceId = this.getAttribute('data-service-id');
                                const csrfToken = this.getAttribute(`data-token-${serviceId}`);

                                const editService = $('.edit-service-container');

                                const url = `/service/form/generate/${serviceId}`;
                                const response = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken // Ajouter le token CSRF dans l'en-tête
                                    },
                                    // Inclure le token CSRF dans le corps de la requête
                                    body: JSON.stringify({ _token: csrfToken })
                                });

                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                const contentType = req.headers.get('content-type');
                                // s'il y' a un contenu et de type json dans le header
                                if (contentType && contentType.indexOf('application/json') !== -1) {
                                    // on decode le contenu
                                    const data = await response.json();
                                    console.log('Form data HTML receiver for service:', data);
                                    if (!editService.is(':visible')) {
                                        editService.slideDown(400);
                                    }
                                    // on inject le résultat
                                    document.getElementById('service-form-container').innerHTML = data.formHtml;

                                } else {
                                    const text = await req.text();
                                    console.error('Unexpected response format:', text);
                                }
                            } catch (error) {
                                console.error('Failed to fetch or parse JSON for service form:', error);
                            }
                        });
                    });
                    // Ajouter un écouteur d'événement sur chaque bouton delete
                    document.querySelectorAll('.toggle-trash-service').forEach(button => {
                        button.addEventListener('click', async function () {
                            const serviceId = this.getAttribute('data-service-id');
                            const csrfToken = this.getAttribute(`data-token-${serviceId}`);
                            // Confirmation JS (prévoir custom)
                            if (confirm('Confirmez-vous la suppression du service?')) {
                                try {
                                    const response = await fetch(`/serviceItem/delete/${serviceId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken // Ajouter le token CSRF dans l'en-tête
                                        },
                                        // Inclure le token CSRF dans le corps de la requête
                                        body: JSON.stringify({ _token: csrfToken })
                                    });

                                    if (!response.ok) {
                                        throw new Error(`HTTP error! Status: ${response.status}`);
                                    }
                                    const result = await response.json();
                                    console.log(result);

                                    if (result.error) {
                                        console.error(`Error: ${result.error}`);
                                        alert(`Error: ${result.error}`);
                                    } else {
                                        console.log(result.message);
                                        alert(result.message);
                                        this.closest('tr').remove();
                                    }
                                } catch (error) {
                                    console.error('Failed to delete service:', error);
                                    alert('Failed to delete service');
                                }
                            }
                        });
                    });

                });


            } else {
                const text = await req.text();
                console.error('Unexpected response format:', text);
            }
        } catch (error) {
            console.error('Failed to fetch or parse JSON:', error);
        }
    };

    // déroule le formulaire de soumission d'un nouveau service de la page profil
    $('.toggle-service-form').on('click', function () {

        const serviceForm = $('.service-form');
        const listeServices = $('.list-services');

        if (serviceForm.is(':visible')) {
            serviceForm.slideUp(400, function () {
                $('.toggle-service-form').text('Nouveau service');

            });
        } else {
            serviceForm.slideDown(400, function () {
                $('.toggle-service-form').text('Fermer le formulaire');
            });
            if (listeServices.is(':visible')) {
                listeServices.slideUp(400, function () {
                    $('.toggle-list-services').text('Liste de mes services');
                });
            }
        }
    });

    // En cas d'erreur ajuster le text du bouton
    if (serviceForm) {
        if (serviceForm.style.display == 'block') {
            $('.toggle-service-form').text('Fermer le formulaire');
        }
    }
    // ouvre et referme la liste des services
    $('.toggle-list-services').on('click', function () {
        const listeServices = $('.list-services');
        const serviceForm = $('.service-form');
        // on mets a jour la liste des services
        updateListeServices();

        if (listeServices.is(':visible')) {
            listeServices.slideUp(400, function () {
                $('.toggle-list-services').text('Liste de mes services');
            });
        } else {

            listeServices.slideDown(400, function () {
                $('.toggle-list-services').text('Fermer la liste');
            });
            if (serviceForm.is(':visible')) {
                serviceForm.slideUp(400, function () {
                    $('.toggle-service-form').text('Nouveau service');
                });
            }
        }
    });

    // EventListener sur le select Thème
    if (service_item_course_theme) {
        service_item_course_theme.addEventListener('change', updateCategories);
    }
    // EventListener sur le select Catégories
    if (service_item_course_category) {
        service_item_course_category.addEventListener('change', updateCourses);
    }
    // Ajoutez un écouteur d'événements pour le bouton de fermeture
    const closeEdit = document.getElementById('close-edit');
    if (closeEdit) {
        closeEdit.addEventListener('click', () => {
            $('.edit-service-container').slideUp();
        });
    }
});

