import { useState, useEffect } from "react";
/**
 *
 * @param {*} url
 * @param {*} formdata
 * @param {*} csrfToken
 * @param {*} asJson
 * @returns
 */
export const usePostData = (
  url = '',
  formdata = false,
  csrfToken = false,
  asJson = false
) => {
  const [data, setData] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);
  useEffect(() => {
    const postData = async () => {
      console.log(`usePostData called with URL: ${url}, Formdata:`, formdata, `CSRF Token: ${csrfToken}`);

      if (!url || !formdata) {
        console.log("Skipping fetch due to missing URL or Formdata");
        return;
      }

      let body;
      let headers = {
        "X-CSRF-TOKEN": csrfToken, // Ajouter le token CSRF dans l'en-tête
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
          method: "POST",
          headers: headers,
          body: body,
        });
        if (!response.ok) {
          throw new Error(
            `Network response was not ok: ${response.statusText}`
          );
        }
        const result = await response.json();
        console.log("Fetch result:", result);
        setData(result);
      } catch (err) {
        setError(err);
      } finally {
        setLoading(false);
      }
    };

    if (url && formdata) {
      postData();
    }
  }, [url, formdata, csrfToken, asJson]);

  return { data, error, loading };
};
