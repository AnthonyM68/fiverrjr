/**
 * Fonction pour effectuer une requête POST et gérer l'état des données et des erreurs.
 * @param {string} url - L'URL de l'API à laquelle effectuer la requête.
 * @param {FormData} formData - Les données du formulaire à envoyer.
 * @returns {Promise<Object>} - Une Promise contenant les données récupérées et les erreurs survenues.
 */
export const postData = async (url, formData) => {
    console.log(`Fetch posting data to ${url}`);
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,// on ajoute le formulaire
            headers: {
                // Indique que c'est une requête AJAX
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Fetch data received:', data);
        return { data, error: null };

    } catch (error) {
        console.error('Post error:', error);
        return { data: null, error };
    }
};
