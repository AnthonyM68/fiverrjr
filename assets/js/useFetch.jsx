import { useState, useEffect } from "react";

/**
 * hook personnalisé pour effectuer des requêtes post avec gestion de l'état.
 * @param {string} url - l'url de la requête post.
 * @param {FormData|Object} formdata - les données à envoyer avec la requête.
 * @param {string|boolean} csrfToken - le token csrf à inclure dans l'en-tête.
 * @param {boolean} asJson - indique si les données doivent être envoyées en json.
 * @returns {Object} - un objet contenant les données, les erreurs et l'état de chargement.
 */
export const usePostData = (
  url = '',
  formdata = false,
  csrfToken = false,
  asJson = false
) => {
  const [data, setData] = useState(null); // état pour les données de la réponse
  const [error, setError] = useState(null); // état pour les erreurs
  const [loading, setLoading] = useState(true); // état pour le chargement

  useEffect(() => {
    // fonction pour effectuer la requête post
    const postData = async () => {
      console.log(`usePostData called with URL: ${url}, Formdata:`, formdata, `CSRF Token: ${csrfToken}`);

      if (!url || !formdata) {
        console.log("skipping fetch due to missing url or formdata");
        return;
      }

      let body; // corps de la requête
      let headers = {
        "X-CSRF-TOKEN": csrfToken, // ajouter le token csrf dans l'en-tête
      };

      if (asJson) {
        // convertir formdata en json si nécessaire
        if (formdata instanceof FormData) {
          const formObject = {};
          formdata.forEach((value, key) => {
            formObject[key] = value;
          });
          formObject["_token"] = csrfToken;
          body = JSON.stringify(formObject); // convertir l'objet en json
        } else {
          formdata["_token"] = csrfToken;
          body = JSON.stringify(formdata); // convertir l'objet en json
        }
        headers["Content-Type"] = "application/json"; // définir l'en-tête de type json
      } else {
        // ajouter le token csrf aux données du formulaire
        if (formdata instanceof FormData) {
          if (csrfToken) {
            formdata.append("_token", csrfToken);
          }
          body = formdata; // utiliser formdata directement
        } else {
          const formdatas = new FormData();
          for (const key in formdata) {
            if (formdata.hasOwnProperty(key)) {
              formdatas.append(key, formdata[key]); // ajouter chaque paire clé/valeur au formdata
            }
          }
          if (csrfToken) {
            formdatas.append("_token", csrfToken); // ajouter le token csrf au formdata
          }
          body = formdatas; // utiliser formdata comme corps de la requête
        }
      }

      try {
        const response = await fetch(url, {
          method: "POST",
          headers: headers, // en-têtes de la requête
          body: body, // corps de la requête
        });

        if (!response.ok) {
          throw new Error(`network response was not ok: ${response.statusText}`);
        }

        const result = await response.json(); // convertir la réponse en json
        console.log("fetch result:", result);
        setData(result); // mettre à jour l'état avec les données de la réponse
      } catch (err) {
        setError(err); // mettre à jour l'état avec l'erreur
      } finally {
        setLoading(false); // fin de chargement
      }
    };

    if (url && formdata) {
      postData(); // appeler la fonction pour effectuer la requête
    }
  }, [url, formdata, csrfToken, asJson]); // dépendances de useEffect

  return { data, error, loading }; // retourner les états
};
