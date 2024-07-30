import { useState, useEffect } from 'react';

/**
 * Hook personnalisé pour effectuer une requête fetch et gérer l'état des données et des erreurs.
 * @param {string} url - L'URL de l'API à laquelle effectuer la requête.
 * @returns {Object} - Un objet contenant les données récupérées et toute erreur survenue.
 */
const useFetch = (url) => {
    const [data, setData] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        console.log(`Fetching data from ${url}...`);
        const fetchData = async () => {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ` + response.statusText);
                }
                const data = await response.json();
                if(data.services) {
                    // Suppose the API response has a key `services` which is the array we need
                    setData(data.services || []);
                    console.log('Data fetched after json:', data.services);
                } else {
                    setData(data);
                    console.log('Data fetched after json:', data);
                }

            } catch (error) {
                console.error('Fetch error:', error);
                setError(error);
            }
        };
        fetchData();
    }, [url]);

    return { data, error };
};

export default useFetch;
