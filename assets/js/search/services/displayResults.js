export const displayResults = (results, searchTerm) => {
    // déclare et initialise une chaine de caractère vide
    let resultsHtml = '';
    // s'il y'a des résultats dans notre tableau
    if (!results || results.length === 0) {
        resultsHtml = '<h2>Aucun résultat</h2>';
    } else {
        // on convertit le terme rechercher en minuscule
        const searchTermLower = searchTerm.toLowerCase();
        // on itère sur les résultats
        results.forEach(entityTheme => {
            let displayTheme = false;
             // déclare et initialise une chaine de caractère vide qui contiendra les theme
            let themeHtml = '';
            // Si nous avons un nom de theme
            if (entityTheme && entityTheme.nameTheme) {
                // on crée une liste
                themeHtml += `<div class="ui list arborescence">`;
                // on ajoute un item, et affichons le nom de theme
                themeHtml += `<div class="item">
                <i class="folder open outline icon"></i>
                <div class="content">
                <div class="header"><strong>Thème : ${entityTheme.nameTheme}</strong></div>
                </div>
                </div>`;
                // on ajoute une class spécifique a l'affichage d'une arborescence
                themeHtml += `<div class="ui list arborescence">`;
                // on itère sur les theme a la recherche des category
                entityTheme.categories.forEach(category => {
                    // on initialise un boléen 
                    let displayCategory = false;  
                    // une chaine vide...  
                    let categoryHtml = '';
                    // on ajoute un nouvel item, et affichons le nom de category
                    categoryHtml += `<div class="item">
                        <i class="folder open outline icon"></i>
                        <div class="content">
                        <div class="header"><strong>Catégorie : ${category.nameCategory}</strong></div>
                        </div>
                        </div>`;
                    // la class de l'arborescence
                    categoryHtml += `<div class="ui list arborescence">`;
                    // on itère sur les category a la recherche de course
                    category.courses.forEach(course => {
                        // on initialise le boleen
                        let displayCourse = false;
                        // la chaine vide
                        let courseHtml = '';
                        // on crée un nouvel item et affichons le nom de course
                        courseHtml += `<div class="item">
                        <i class="folder open outline icon"></i>
                        <div class="content">
                        <div class="header"><strong>Sous-catégorie : ${course.nameCourse}</strong></div>
                        </div>
                        </div>`;
                        // la class arborescence
                        courseHtml += `<div class="ui list arborescence">`;
                        // on itère sur les course a la recherche de services
                        course.serviceItems.forEach(serviceItem => {
                            // si le terme recherché convertit en minuscule est inclus dans le title
                            if (serviceItem.title.toLowerCase().includes(searchTermLower) ||
                            // ou si le terme convertit en minuscule est inclus dans la description
                                serviceItem.description.toLowerCase().includes(searchTermLower)) {
                                // on change l'état ou la valeur des boléen
                                displayTheme = true;
                                displayCategory = true;
                                displayCourse = true;
                                // on ajoute un dernier item pour afficher le title et description du service
                                courseHtml += `<div class="item">
                                <i class="file icon"></i>
                                <div class="content">
                                <div class="header"><a href="/detail_service/${serviceItem.id}">${serviceItem.title}</a> Par: ${serviceItem.user.username}</div>
                                <div class="description">
                                ${serviceItem.description}
                                </div>
                                </div>
                                </div>`;
                            }
                        });
                        // si displayCourse vaux true alors on ajoute la chaine course contenant les course
                        if (displayCourse) {
                            categoryHtml += courseHtml + `</div>`; // sous-catégorie
                        }
                    });
                    // si displayCategory vaux true alors on ajoute la chaine category contenant les category
                    if (displayCategory) {
                        themeHtml += categoryHtml + `</div>`; // catégorie
                    }
                });
                // si displayTheme vaux true alors on ajoute la chaine theme contenant les theme
                if (displayTheme) {
                    resultsHtml += themeHtml + `</div>`; // thème
                }
            }
        });
    }
    // on retourne la chaine complète
    return resultsHtml;
};