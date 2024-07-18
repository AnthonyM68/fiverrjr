document.addEventListener('DOMContentLoaded', () => {
    console.log('=> ViewServiceForm.js loaded!');
    const service_item_course_theme = document.getElementById('service_item_course_theme');
    const service_item_course_category = document.getElementById('service_item_course_category');
    const service_item_course_course = document.getElementById('service_item_course_course');
    const service_item_list = document.getElementById('service_item_list');

    if (!service_item_course_theme || !service_item_course_category || !service_item_course_course) {
        console.error('Form elements not found');
        return;
    }
    const updateForm = async (url) => {
        try {
            const req = await fetch(url);
            return await req.json();
        } catch (error) {
            console.error('Failed to fetch or parse JSON:', error);
            return [];
        }
    };

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
        const userRating = document.querySelector('.js-user-list-service');
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

                    // Bouton d'édition
                    const editButton = document.createElement('a');
                    editButton.className = 'ui-button ui-widget ui-corner-all toggle-edit-service';
                    editButton.innerHTML = '<span class="ui-icon ui-icon-pencil"></span>';
                    editButton.setAttribute('href', "javascript:void(0);");
                    editButton.setAttribute('data-service-id', service.id); // Ajout de l'attribut data-service-id avec l'ID du service
                    buttonGroup.appendChild(editButton);

                    tdButtons.appendChild(buttonGroup);
                    tr.appendChild(tdButtons);

                    service_item_list.appendChild(tr);
                });

                // Ajouter un écouteur d'événement sur chaque bouton d'édition
                document.querySelectorAll('.toggle-edit-service').forEach(button => {
                    button.addEventListener('click', async function () {
                        try {
                            const serviceId = this.getAttribute('data-service-id');
                            const editService = $('.edit-service-container');
                            const url = `/service/form/generate/${serviceId}`;

                            const req = await fetch(url);
                            if (!req.ok) {
                                throw new Error(`HTTP error! Status: ${req.status}`);
                            }
                            const contentType = req.headers.get('content-type');

                            if (contentType && contentType.indexOf('application/json') !== -1) {
                                const data = await req.json();
                                console.log('Form data for service:', data);

                                if (editService.is(':visible')) {
                                    editService.slideUp(400, function () {
                                        document.getElementById('service-form-container').innerHTML = data.formHtml;
                                    });
                                } else {
                                    editService.slideDown(400, function () {
                                        document.getElementById('service-form-container').innerHTML = data.formHtml;
                                    });
                                }
                            } else {
                                const text = await req.text();
                                console.error('Unexpected response format:', text);
                            }
                        } catch (error) {
                            console.error('Failed to fetch or parse JSON for service form:', error);
                        }
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


    const loadServiceForm = async () => {
        const url = '/service/form/generate'; // L'URL définie pour obtenir le formulaire

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            console.log(data);

            document.querySelector('#service-form-container').innerHTML = data.formHtml;
        } catch (error) {
            console.error('Failed to load the service form:', error);
        }
    };







    if (service_item_course_theme) {
        service_item_course_theme.addEventListener('change', updateCategories);
    }

    if (service_item_course_category) {
        service_item_course_category.addEventListener('change', updateCourses);
    }







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
    // ouvre et referme la liste des services
    $('.toggle-list-services').on('click', function () {
        const listeServices = $('.list-services');
        const serviceForm = $('.service-form');

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
    // // Ajoutez un écouteur d'événements pour le bouton de fermeture
    const closeEdit = document.getElementById('close-edit');
    if (closeEdit) {
        closeEdit.addEventListener('click', () => {
            $('.edit-service-container').slideUp();
        });
    }
});

