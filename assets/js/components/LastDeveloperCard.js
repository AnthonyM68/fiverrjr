import React from 'react';
import { Card, Image } from 'semantic-ui-react';

const LastDeveloperCard = ({ developer }) => {
    
    const { picture, firstName, lastName, username, dateRegister, bio } = developer;

    return (
        <Card>
            {picture && <Image src={picture} wrapped ui={false} />}
            <Card.Content>
                <Card.Header>
                    <span>{firstName} {lastName}</span>
                    <span>{username}</span>
                </Card.Header>
                <Card.Meta>
                    <span className='date'>Date d'inscription: {dateRegister}</span>
                </Card.Meta>
                <Card.Description>{bio}</Card.Description>
            </Card.Content>
        </Card>
    );
};


export default LastDeveloperCard;
