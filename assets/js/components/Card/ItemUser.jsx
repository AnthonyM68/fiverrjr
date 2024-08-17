import React from "react";
import { createRoot } from 'react-dom/client';

import {
  CardMeta,
  CardHeader,
  CardDescription,
  CardContent,
  Card,
  Icon,
  Image,
} from "semantic-ui-react";

const CardUser = ({ user }) => {
  // Déstructuration des propriétés de l'objet user
  const { picture, firstName, lastName, username, dateRegister, bio } = user;
  // Vérifiez si dateRegister est une date valide
  const registerDate = new Date(dateRegister);
  const isValidDate = !isNaN(registerDate.getTime());
  return (
    <Card className="margin-bottom-large">
      {/* Image du profil */}
      {/* Conteneur pour l'image du profil avec des dimensions fixes */}
      {picture && (
        <div className="image-container">
          <Image
            src={picture}
            className="profile-image"
            alt={`Image de profil de ${firstName} ${lastName}`}
          />
        </div>
      )}
      <CardContent>
        {/* Nom et prénom */}
        <CardHeader>
          {firstName} {lastName}
        </CardHeader>

        {/* Métadonnées (peut-être la date d'inscription) */}
        <CardMeta>
          <span className="date">
            Inscrit depuis: {registerDate.getFullYear()}{" "}
          </span>
          <span className="date">ItemUser</span>
        </CardMeta>

        {/* Description */}
        <CardDescription>{bio}</CardDescription>
      </CardContent>

      {/* Contenu supplémentaire (comme le nombre d'amis) */}
      <CardContent extra>
        <a>
          <Icon name="user" />
          22 Services
        </a>
      </CardContent>
    </Card>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  const lastDeveloperRoot = document.getElementById('last-developer-profile');
  if (lastDeveloperRoot) {
      const root = createRoot(lastDeveloperRoot);
      // on récupère les données du dernier utilisateur depuis le template user/index.html.twig
      root.render(<CardUser user={window.__INITIAL_DATA__.lastDeveloper} />);
  }
  /**
   * REACT dernier client affichage de la carte dans le profil utilisateur
   */
  const lastClientRoot = document.getElementById('last-client-profile');
  if (lastClientRoot) {
      const root = createRoot(lastClientRoot);
      // on récupère les données du dernier utilisateur depuis le template user/index.html.twig
      root.render(<CardUser user={window.__INITIAL_DATA__.lastClient} />);
  }
});



export {CardUser};
