/**
 * Fonction pour effectuer une requête POST et gérer l'état des données et des erreurs.
 * @param {string} url - L'URL de l'API à laquelle effectuer la requête.
 * @param {FormData | Object} data - Les données du formulaire à envoyer.
 * @param {string} csrfToken - Le token CSRF à envoyer.
 * @param {boolean} asJson - Indique si les données doivent être envoyées en JSON.
 * @returns {Promise<Object>} - Une Promise contenant les données récupérées et les erreurs survenues.
 */
export const usePostData = async (url, data, csrfToken = false, asJson = false) => {
    console.log(`postData.js: Starting POST request to ${url}`);
    // console.log(`postData.js: CSRF Token provided: ${csrfToken}`);
    // console.log(`postData.js: Sending data as JSON: ${asJson}`);

    let body;
    let headers = {
        'X-CSRF-TOKEN': csrfToken, // Ajoute le token CSRF dans l'en-tête
    };

    if (asJson) {
        console.log("postData => Converting data to JSON format.");
        // Convertir FormData en objet JSON si nécessaire
        if (data instanceof FormData) {
            console.log("postData => Data is of type FormData, converting to JSON object.");
            const formObject = {};
            data.forEach((value, key) => {
                // Ajouter chaque paire clé/valeur à l'objet
                formObject[key] = value;
                // Si le champ actuel est le token CSRF, mettre à jour sa valeur
                if (csrfToken && key.includes('_token')) {
                    formObject[key] = csrfToken;
                }
            });
            body = JSON.stringify(formObject); // Convertir l'objet en JSON

        } else {
            console.log("postData => Data is already an object, adding CSRF token if necessary.");
            if (csrfToken) {
                data['search_form[_token]'] = csrfToken; // Ajouter le token CSRF à l'objet directement
            }
            body = JSON.stringify(data); // Convertir l'objet en JSON
        }
        headers['Content-Type'] = 'application/json'; // Définir l'en-tête de type JSON
        console.log("postData => Headers set for JSON:", headers);
    }
    else {
        console.log("postData => Sending data as FormData.");

        // On vérifie le format de data
        if (data instanceof FormData) {
            console.log("postData => Data is of type FormData.");
            // if (csrfToken) {
            //     data.append('_token', csrfToken); // on ajoute le token CSRF au FormData
            // }
            for (const pair of data.entries()) {
                console.log(`FormData entry => {${pair[0]}: ${pair[1]}}`);
            }
            body = data; // on utilise le FormData comme corps de la requête
        } else {
            console.log("postData => Data is of type Object, converting to FormData.");
            // Convertir l'objet en FormData
            const formData = new FormData();
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    formData.append(key, data[key]); // Ajouter chaque paire clé/valeur au FormData
                }
            }
            if (csrfToken) {
                formData.append('_token', csrfToken); // Ajouter le token CSRF au FormData
            }
            body = formData; // Utiliser le FormData comme corps de la requête
        }
    }

    try {
        console.log("postData => Sending POST request.");
        const response = await fetch(url, {
            method: 'POST',
            headers: headers, 
            body: body, // Utiliser le corps de la requête préparé
        });

        if (!response.ok) {
            console.error(`postData => Network response was not ok: ${response.statusText}`);
            throw new Error(`Network response was not ok: ${response.statusText}`); // Gérer les erreurs de réseau
        }

        const responseData = await response.json(); // Convertir la réponse en JSON
        console.log('postData => Received response data:', responseData);

        // Vérifier si la réponse indique un succès ou une erreur
        if (responseData.success === false) {
            console.error('postData => Server error:', responseData.error.message);
            return { data: null, error: responseData.error }; // Retourner les détails de l'erreur côté serveur
        }

        return { data: responseData, error: null }; // Retourner les données récupérées si tout est OK


    } catch (error) {
        console.error('postData => Error during POST request:', error.message); // Gérer les erreurs de la requête
         return { data: null, error: { message: error.message } }; // Retourner l'erreur capturée
    }
};
