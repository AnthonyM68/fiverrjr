export const displayResults = (results, searchTerm) => {
    console.log(results);
    let resultsHtml = '';

    if (!results || results.length === 0) {
        resultsHtml = '<h2>Aucun résultat</h2>';
    } else {
        resultsHtml += '<h3>Résultats</h3>';
        // on convertit le terme rechercher en minuscule
        const searchTermLower = searchTerm.toLowerCase();
        results.forEach(entityTheme => {
            let displayTheme = false;
            let themeHtml = '';

            if (entityTheme && entityTheme.nameTheme) {
                console.log(entityTheme.nameTheme);


                themeHtml += `<div class="ui list arborescence">`;

                themeHtml += `<div class="item">
                <i class="folder open outline icon"></i>
                <div class="content">
                <div class="header"><strong>Thème : ${entityTheme.nameTheme}</strong></div>
                </div>
                </div>`;

                themeHtml += `<div class="ui list arborescence">`;

                entityTheme.categories.forEach(category => {
                    let displayCategory = false;
                    let categoryHtml = '';

                    categoryHtml += `<div class="item">
                        <i class="folder open outline icon"></i>
                        <div class="content">
                        <div class="header"><strong>Catégorie : ${category.nameCategory}</strong></div>
                        </div>
                        </div>`;

                    categoryHtml += `<div class="ui list arborescence">`;

                    category.courses.forEach(course => {
                        let displayCourse = false;
                        let courseHtml = '';

                        courseHtml += `<div class="item">
                        <i class="folder open outline icon"></i>
                        <div class="content">
                        <div class="header"><strong>Sous-catégorie : ${course.nameCourse}</strong></div>
                        </div>
                        </div>`;

                        courseHtml += `<div class="ui list arborescence">`;

                        course.serviceItems.forEach(serviceItem => {
                            if (serviceItem.title.toLowerCase().includes(searchTermLower) ||
                                serviceItem.description.toLowerCase().includes(searchTermLower)) {
                                displayTheme = true;
                                displayCategory = true;
                                displayCourse = true;
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

                        if (displayCourse) {
                            categoryHtml += courseHtml + `</div>`; // sous-catégorie
                        }
                    });

                    if (displayCategory) {
                        themeHtml += categoryHtml + `</div>`; // catégorie
                    }
                });

                if (displayTheme) {
                    resultsHtml += themeHtml + `</div>`; // thème
                }
            }
        });
    }
    return resultsHtml;
};