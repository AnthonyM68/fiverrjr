import React from "react";
import { Card, Image} from 'semantic-ui-react'

const UserCard = ({
  user = {
    picture: null,
    firstName: "John",
    lastName: "Doe",
    username: "johndoe",
    dateRegister: new Date().toISOString(),
    bio: "This is a bio",
  },
}) => {
  const { picture, firstName, lastName, username, dateRegister, bio } = user;

  const formattedDate = new Intl.DateTimeFormat("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  }).format(new Date(dateRegister));

  return (
    <Card fluid>
      <div 
      className="image-container"
      style={{ display: 'flex', alignItems: 'flex-start' }}
      >
      {picture && (
          <Image
            src={picture}
            wrapped
            ui={true}
            className="profile-image"
            alt={`Image de profil de ${firstName} ${lastName}`}
          />
        )}
        
        <Card.Content style={{ flex: 1, textAlign: 'left' }}>
          <Card.Header>
            <p>
              {firstName} {lastName}
            </p>
            <p>{username}</p>
          </Card.Header>
          <Card.Meta>
            <span className="date">Date d'inscription: {formattedDate}</span>
            <span className="date">UserCard</span>
          </Card.Meta>
          <Card.Description>{bio}</Card.Description>
        </Card.Content>
      </div>
    </Card>
  );

};

export default UserCard;
