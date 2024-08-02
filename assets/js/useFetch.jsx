import { useState, useEffect } from 'react';

/**
 * Hook personnalisé pour effectuer une requête POST et gérer l'état des données et des erreurs.
 * @param {string} url - L'URL de l'API à laquelle effectuer la requête.
 * @param {FormData} formData - Les données du formulaire à envoyer.
 * @returns {Object} - Un objet contenant les données récupérées et toute erreur survenue.
 */
const usePost = (url, formData) => {
    const [data, setData] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        const postData = async () => {
            console.log(`Posting data to ${url}...`);
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                });
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ` + response.statusText);
                }
                const data = await response.json();
                setData(data);
                console.log('Data posted and response received:', data);
            } catch (error) {
                console.error('Post error:', error);
                setError(error);
            }
        };

        if (formData) {
            postData();
        }
    }, [url, formData]);

    return { data, error };
};

export default usePost;
