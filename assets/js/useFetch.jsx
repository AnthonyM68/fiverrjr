import { useState, useEffect } from "react";

/**
 * Hook personnalisé pour effectuer une requête POST et gérer l'état des données et des erreurs.
 * @param {string} url - L'URL de l'API à laquelle effectuer la requête.
 * @param {FormData} formData - Les données du formulaire à envoyer.
 * @returns {Object} - Un objet contenant les données récupérées et toute erreur survenue.
 */
export const usePostData = (url, formdata, csrfToken = false, asJson = false) => {
  console.log(`useFetch.jsx posting data to ${url}`);

  const [data, setData] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const postData = async () => {
      if (!url || !formdata) {
        // Ne pas exécuter si url ou formdata sont manquants
        // (recherche sur multiple appel a usePostData)
        return;
      }

      let body;
      let headers = {
        'X-CSRF-TOKEN': csrfToken, // Ajouter le token CSRF dans l'en-tête
      };

      if (asJson) {
        // Convertir FormData en objet JSON si nécessaire
        if (formdata instanceof FormData) {
          const formObject = {};
          formdata.forEach((value, key) => {
            formObject[key] = value;
          });
          formObject["_token"] = csrfToken;
          body = JSON.stringify(formObject);
        } else {
          formdata["_token"] = csrfToken;
          body = JSON.stringify(formdata);
        }
        headers["Content-Type"] = "application/json";
      } else {
        // Ajouter le token CSRF aux données du formulaire
        if (formdata instanceof FormData) {
          if (csrfToken) {
            formdata.append("_token", csrfToken);
          }
          body = formdata;
        } else {
          const formdatas = new FormData();
          for (const key in formdata) {
            if (formdata.hasOwnProperty(key)) {
              formdatas.append(key, formdata[key]);
            }
          }
          if (csrfToken) {
            formdatas.append("_token", csrfToken);
          }
          body = formdatas;
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
        const result = await response.json();
        console.log(result);
        setData(result);
      } catch (err) {
        setError(err);
      } finally {
        setLoading(false);
      }
    };

    postData();
  }, [url, formdata, csrfToken, asJson]);

  return { data, error, loading };
};
