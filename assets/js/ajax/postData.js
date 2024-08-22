/**
 * fonction pour effectuer une requête post et gérer l'état des données et des erreurs
 * @param {string} url - l'url de l'api à laquelle effectuer la requête
 * @param {FormData | Object} data - les données du formulaire à envoyer
 * @param {string} csrfToken - le token csrf à envoyer
 * @param {boolean} asJson - indique si les données doivent être envoyées en json
 * @returns {Promise<Object>} - une promise contenant les données récupérées et les erreurs survenues
 */
export const usePostData = async (url, data, csrfToken = false, asJson = false) => {
    console.log(`postData.js: starting post request to ${url}`);
    let body;
    let headers = {
        'X-CSRF-TOKEN': csrfToken, // protection csrf
    };
    if (asJson) {
        console.log("postData => converting data to json format.");
        if (data instanceof FormData) {
            console.log("postData => data is of type formdata, converting to json object.");
            const formObject = {};
            data.forEach((value, key) => {
                formObject[key] = key.includes('_token') && csrfToken ? csrfToken : value; // gestion du token csrf
            });
            body = JSON.stringify(formObject); // conversion en json
        } else {
            if (csrfToken) {
                data['search_form[_token]'] = csrfToken; // ajout du token csrf si nécessaire
            }
            body = JSON.stringify(data); // conversion en json
        }
        headers['Content-Type'] = 'application/json'; // définition de l'en-tête json
    } else {
        console.log("postData => sending data as formdata.");

        if (data instanceof FormData) {
            body = data; // envoi direct du formdata
        } else {
            const formData = new FormData();
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    formData.append(key, data[key]); // conversion de l'objet en formdata
                }
            }
            if (csrfToken) {
                formData.append('_token', csrfToken); // ajout du token csrf au formdata
            }
            body = formData; // utilisation du formdata comme corps de requête
        }
    }
    try {
        console.log("postData => sending post request.");
        const response = await fetch(url, {
            method: 'POST',
            headers: headers, 
            body: body, // corps de la requête préparé
        });
        if (!response.ok) {
            console.error(`postData => network response was not ok: ${response.statusText}`);
            throw new Error(`network response was not ok: ${response.statusText}`); // gérer les erreurs réseau
        }
        const responseData = await response.json(); // conversion de la réponse en json
        console.log('postData => received response data:', responseData);

        if (responseData.success === false) {
            console.error('postData => server error:', responseData.error.message);
            return { data: null, error: responseData.error }; // retour des erreurs côté serveur
        }
        return { data: responseData, error: null }; // retour des données si tout est correct
    } catch (error) {
        console.error('postData => error during post request:', error.message); // gestion des erreurs de la requête
        return { data: null, error: { message: error.message } }; // retour de l'erreur capturée
    }
};
