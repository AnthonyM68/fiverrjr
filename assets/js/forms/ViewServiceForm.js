/**
 * Gestion des contenu dynamique des select /service/new 
 * Formulaire ServiceThemeCategoryCourseType
 */
(function () {
    document.addEventListener('DOMContentLoaded', () => {
        // Sélection des éléments du formulaire
        const form_select_theme = document.getElementById('service_course_theme');
        const form_select_category = document.getElementById('service_course_category');
        const form_select_course = document.getElementById('service_course_course');

        if (!form_select_theme || !form_select_category || !form_select_course) {
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

        // Fonction pour mettre à jour les catégories dynamiquement
        const updateCategories = async (e) => {
            const themeId = e.target.value;
            console.log('Selected theme ID:', themeId);
            const url = `/categories_by_theme/${themeId}`;
            const categories = await updateForm(url);
            console.log(categories);

            form_select_category.innerHTML = '';
            // On boucle sur les categories et remplissons le champs d'options
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                form_select_category.appendChild(option);
            });
            // Afficher le champ Category une fois que les options sont chargées
            updateCourses();
        };

        // Fonction pour mettre à jour les cours dynamiquement
        const updateCourses = async () => {
            const categoryId = form_select_category.value;
            const url = `/courses_by_category/${categoryId}`;

            const courses = await updateForm(url);
            console.log('Courses:', courses);

            form_select_course.innerHTML = '';
            // On boucle sur les courses et remplissons le champs d'options
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.id;
                option.textContent = course.name;
                form_select_course.appendChild(option);
            });
        };

        form_select_theme.addEventListener('change', updateCategories);
        form_select_category.addEventListener('change', updateCourses);
    });
})();