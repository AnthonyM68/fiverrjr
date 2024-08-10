import React, { useEffect, useState } from "react";
import { createRoot } from "react-dom/client";
import { useInView } from "react-intersection-observer";
import { showAlert } from "./../../alert/messageFlash.js";
import { usePostData } from "../../useFetch";
import UserCard from "../Card/ItemUser"; // Assurez-vous que le chemin est correct
import "uikit/dist/css/uikit.min.css";
import UIkit from "uikit";

// Composant principal pour la recherche de développeur
const DeveloperSearch = () => {
  const [tokens, setTokens] = useState({
    searchItemUserToken: "",
    searchItemCityToken: "",
  });
  const [urls, setUrls] = useState({
    searchDeveloperByNameUrl: "",
    searchDeveloperByCityUrl: "",
  });
  const [formData, setFormData] = useState(null);
  const [csrfToken, setCsrfToken] = useState(null);
  const [formAction, setFormAction] = useState("");
  const [showResults, setShowResults] = useState(false);
  const [initialLoading, setInitialLoading] = useState(true);

  // Utilisation du hook personnalisé pour les requêtes POST
  const { data, error, formLoading } = usePostData(
    formAction,
    formData,
    csrfToken,
    true
  );

  // Effect pour initialiser les tokens et les URLs
  useEffect(() => {
    if (window.__INITIAL_DATA__) {
      setTokens({
        searchItemUserToken: window.__INITIAL_DATA__.searchItemUserToken,
        searchItemCityToken: window.__INITIAL_DATA__.searchItemCityToken,
      });
      setUrls({
        searchDeveloperByNameUrl:
          window.__INITIAL_DATA__.searchDeveloperByNameUrl,
        searchDeveloperByCityUrl:
          window.__INITIAL_DATA__.searchDeveloperByCityUrl,
      });
      setInitialLoading(false);
    } else {
      console.error(
        "window.__INITIAL_DATA__ is not defined or does not contain required tokens or URLs"
      );
    }
  }, []);

  // Gestion de la soumission du formulaire par nom
  const handleSubmitByName = (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const csrfToken = formData.get("_token");
    const searchTerm = formData.get("search-user-by-name");

    if (!csrfToken) {
      console.warn("CSRF token is missing");
      showAlert("negative", "Token CSRF manquant");
      return;
    }

    if (!searchTerm) {
      showAlert("warning", "Vous n'avez pas indiqué de mot clé de recherche");
      return;
    }

    setFormData(formData);
    setCsrfToken(csrfToken);
    setFormAction(urls.searchDeveloperByNameUrl);
    setShowResults(true);
  };

  // Gestion de la soumission du formulaire par ville
  const handleSubmitByCity = (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const csrfToken = formData.get("_token");
    const searchTerm = formData.get("search-user-by-city");

    if (!csrfToken) {
      console.warn("CSRF token is missing");
      showAlert("negative", "Token CSRF manquant");
      return;
    }

    if (!searchTerm) {
      showAlert("warning", "Vous n'avez pas indiqué de mot clé de recherche");
      return;
    }

    setFormData(formData);
    setCsrfToken(csrfToken);
    setFormAction(urls.searchDeveloperByCityUrl);
    setShowResults(true);
  };

  // Gestion du clic sur l'icône de recherche
  const handleIconClick = (e, formId) => {
    e.preventDefault();
    document
      .getElementById(formId)
      .dispatchEvent(new Event("submit", { cancelable: true, bubbles: true }));
  };

  // Utilisation de useInView pour l'animation de fade-in
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });

  // Gestion de la fermeture des résultats
  const handleCloseResults = () => {
    setShowResults(false);
    setFormData(null);
    setCsrfToken(null);
    setFormAction("");
    document.getElementById("search-user-by-name").reset();
    document.getElementById("search-user-by-city").reset();
  };

  // Rendu des résultats
  const renderResults = () => {
    console.log(data);

    if (data && showResults) {
      if (data.length > 0) {
        return (
          <div>
            <h2>Résultats de la recherche:</h2>
            <div className="cards-container padding-bottom-large">
              {data.map((item, index) => (
                <UserCard key={index} user={item} />
              ))}
            </div>
            <button className="ui button primary" onClick={handleCloseResults}>
              Fermer
            </button>
          </div>
        );
      } else {
        return (
          <div>
            <h2>Résultats de la recherche:</h2>
            <p>Aucun résultat trouvé.</p>
            <button className="ui button primary" onClick={handleCloseResults}>
              Fermer
            </button>
          </div>
        );
      }
    }
    return null;
  };

  // Affichage des loaders et des messages d'erreur
  if (initialLoading) {
    return <div className="ui active centered inline loader"></div>;
  }
  if (formLoading) {
    return <div className="ui active centered inline loader"></div>;
  }
  if (error) {
    return <div>Error: {error.message}</div>;
  }

  // Rendu principal du composant
  return (
    <div className="ui container">
      <div
        ref={ref}
        className={`ui center aligned message ${
          inView ? "uk-animation-fade" : ""
        }`}
      >
        <h1 className="ui huge header">Trouvez un Développeur</h1>
        <h2 className="ui center aligned icon header">
          <i className="circular users icon"></i>
          <div className="ui grid">
            <div className="eight wide column right aligned margin-bottom-large">
              <div className="ui right aligned category search">
                <form
                  onSubmit={handleSubmitByName}
                  id="search-user-by-name"
                  className="ui form small icon input"
                >
                  <input
                    name="search-user-by-name"
                    type="text"
                    placeholder="Nom, prénom..."
                  />
                  <i
                    className="search link icon search-user"
                    onClick={(e) => handleIconClick(e, "search-user-by-name")}
                  ></i>
                  <input
                    type="hidden"
                    name="_token"
                    value={tokens.searchItemUserToken}
                  />
                </form>
              </div>
            </div>
            <div className="eight wide column left aligned">
              <div className="ui right aligned category search">
                <form
                  onSubmit={handleSubmitByCity}
                  id="search-user-by-city"
                  className="ui form small icon input"
                >
                  <input
                    name="search-user-by-city"
                    type="text"
                    placeholder="Commune..."
                  />
                  <i
                    className="search link icon search-user"
                    onClick={(e) => handleIconClick(e, "search-user-by-city")}
                  ></i>
                  <input
                    type="hidden"
                    name="_token"
                    value={tokens.searchItemCityToken}
                  />
                </form>
              </div>
            </div>
          </div>
        </h2>
        {renderResults()}
      </div>
    </div>
  );
};

// Montage du composant après le chargement du DOM
document.addEventListener("DOMContentLoaded", () => {
  const searchRoot = document.getElementById("developer-search-root");
  if (searchRoot) {
    const root = createRoot(searchRoot);
    root.render(<DeveloperSearch />);
  }
});

export default DeveloperSearch;
