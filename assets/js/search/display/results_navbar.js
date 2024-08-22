export const displayResults = (results) => {
    console.log(results);
    let resultsHtml = '';

    if (!results || results.length === 0) {
        resultsHtml = '<h2>Aucun résultat</h2>';
    } else {
        resultsHtml += '<h3>Résultats de recherche :</h3><div class="ui divided items">';
        
        results.forEach(theme => {
            theme.categories.forEach(category => {
                category.courses.forEach(course => {
                    course.serviceItems.forEach(service => {
                        const page = 1;
                        // Créer le fil d'Ariane pour le service
                        const breadcrumbHtml = `
                            <div class="ui breadcrumb">
                                <a href="/theme/detail/list/category/${theme.id}" class="section">${theme.nameTheme}</a>
                                <i class="right angle icon divider"></i>
                                <a href="/category/detail/list/course/${category.id}" class="section">${category.nameCategory}</a>
                                <i class="right angle icon divider"></i>
                                <a href="/course/detail/list/serviceItem/${page}/${course.id}" class="section">${course.nameCourse}</a>
                                <i class="right angle icon divider"></i>
                                <div class="active section"><a href="/service/detail/${service.id}"><i class="eye icon"></i>${service.title}</a></div>
                            </div>
                        `;

                        // Ajouter l'élément avec le fil d'Ariane
                        resultsHtml += `
                        <div class="item"> 
                            <div class="ui small image">
                                <img src="${service.picture}" alt="Image du service proposé ${service.title}">
                            </div>
                            <div class="content">
                                <div class="header"><strong>${service.title}</strong></div>
                                <div class="meta">
                                    <span class="price"><strong>${service.price}</strong>€</span>
                                    <span class="stay">${breadcrumbHtml}</span>
                                </div>
                                <div class="description">
                                    <p>${service.description || 'Description non disponible'}</p>
                                </div>
                            </div>
                        </div>`;
                    });
                });
            });
        });

        resultsHtml += '</div>';
    }
    
    return resultsHtml;
};
