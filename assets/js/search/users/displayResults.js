export const displayResults = (results, searchTerm) => {
    console.log(results);
    let resultsHtml = '';

    if (!results || results.length === 0) {
        resultsHtml = '<h2>Aucun résultat</h2>';
    } else {
        resultsHtml += '<h3>Résultats</h3><div class="ui divided items">';
        // on convertit le terme rechercher en minuscule
        const searchTermLower = searchTerm.toLowerCase();
        results.forEach(user => {
            resultsHtml += `
            <div class="item">
                <div class="image">
                <img src="${user.user.picture}" alt="Image Developer ${user.user.firstName} ${user.user.lastName}">
                </div>
                <div class="content">
                <a class="header">${user.user.firstName} ${user.user.lastName}</a>
                <div class="meta">
                    <span class="cinema">Inescrit depuis: ${user.formattedDate}</span>
                </div>
                <div class="description">
                    <p>${user.user.bio}</p>
                </div>
                <div class="extra">
                    <a class="ui button label" href="${user.profileUrl}">Profil</a>
                    <a class="ui label" href="${user.listServices}">Services</a>
                </div>
                </div>
            </div>
            `
        });
    }
    return resultsHtml;
};