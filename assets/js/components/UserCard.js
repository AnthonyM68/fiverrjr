import React from 'react';
import { Card, Image } from 'semantic-ui-react';

const UserCard = ({ user }) => {

    const { picture, firstName, lastName, username, dateRegister, bio } = user;

    // Utilisation de l'API Intl pour formater la date en format européen
    const formattedDate = new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(new Date(dateRegister));

    return (
        <Card fluid>
            {picture && <Image src={picture} wrapped ui={false} />}
            <Card.Content>
                <Card.Header>
                    <p>{firstName} {lastName}</p>
                    <p>{username}</p>
                </Card.Header>
                <Card.Meta>
                    <span className='date'>Date d'inscription: {formattedDate}</span>
                </Card.Meta>
                <Card.Description>{bio}</Card.Description>
            </Card.Content>
        </Card>
    );
};


export default UserCard;
