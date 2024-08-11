import React from "react";

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
    <Card>
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
          22 Amis
        </a>
      </CardContent>
    </Card>
  );
};
export default CardUser;
