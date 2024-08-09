/**
 * Fonction pour effectuer une requête POST et gérer l'état des données et des erreurs.
 * @param {string} url - L'URL de l'API à laquelle effectuer la requête.
 * @param {FormData | Object} data - Les données du formulaire à envoyer.
 * @param {string} csrfToken - Le token CSRF à envoyer.
 * @param {boolean} asJson - Indique si les données doivent être envoyées en JSON.
 * @returns {Promise<Object>} - Une Promise contenant les données récupérées et les erreurs survenues.
 */
export const usePostData = async (url, data, csrfToken = false, asJson = false) => {
    console.log(`postData.js posting data to ${url}`);

    let body;
    let headers = {
        'X-CSRF-TOKEN': csrfToken, // Ajouter le token CSRF dans l'en-tête
    };

    if (asJson) {
        // Convertir FormData en objet JSON si nécessaire
        if (data instanceof FormData) {
            const formObject = {};
            data.forEach((value, key) => {
                formObject[key] = value; // Correction ici
            });
            formObject['_token'] = csrfToken;
            body = JSON.stringify(formObject);
        } else {
            data['_token'] = csrfToken;
            body = JSON.stringify(data);
        }
        headers['Content-Type'] = 'application/json';
    } else {
        // Ajouter le token CSRF aux données du formulaire
        if (data instanceof FormData) {
            if (csrfToken) {
                data.append('_token', csrfToken);
            }
            body = data;
        } else {
            // Convertir l'objet en FormData
            const formData = new FormData();
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    formData.append(key, data[key]);
                }
            }
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }
            body = formData;
        }
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: headers,
            body: body,
        });

        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }

        const responseData = await response.json();
        console.log('postData received:', responseData);
        return { data: responseData, error: null };

    } catch (error) {
        console.error('Post error:', error);
        return { data: null, error };
    }
};
