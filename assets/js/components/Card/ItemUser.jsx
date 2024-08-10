import React from 'react'

import {
  CardMeta,
  CardHeader,
  CardDescription,
  CardContent,
  Card,
  Icon,
  Image,
} from 'semantic-ui-react'
const CardUser = ({ user }) => {
  // Déstructuration des propriétés de l'objet user
  const { picture, firstName, lastName, username, dateRegister, bio } = user;

  return (
    <Card>
      {/* Image du profil */}
      <Image src={picture} wrapped ui={false} />

      <CardContent>
        {/* Nom et prénom */}
        <CardHeader>{firstName} {lastName}</CardHeader>

        {/* Métadonnées (peut-être la date d'inscription) */}
        <CardMeta>
          <span className='date'>Inscrit depuis {new Date(dateRegister).getFullYear()}</span>
        </CardMeta>

        {/* Description */}
        <CardDescription>{bio}</CardDescription>
      </CardContent>

      {/* Contenu supplémentaire (comme le nombre d'amis) */}
      <CardContent extra>
        <a>
          <Icon name='user' />
          22 Amis
        </a>
      </CardContent>
    </Card>
  );
};
export default CardUser
