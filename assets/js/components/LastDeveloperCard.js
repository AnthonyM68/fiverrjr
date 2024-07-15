import React from 'react';
import { Card, Image } from 'semantic-ui-react';

const LastDeveloperCard = ({ developer }) => {
    
    return (
        <Card>
            <Image src={developer.picture} wrapped ui={false} />
            <Card.Content>
                <Card.Header>
                    <span>{developer.firstName} {developer.lastName}</span>
                    <span>{developer.username}</span>
                </Card.Header>

                <Card.Meta>
                    <span className='date'>Date d'inscription: {developer.dateRegister}</span>
                </Card.Meta>

                <Card.Description>{developer.description}</Card.Description>
            </Card.Content>
        </Card>
    );
};

// Vous pouvez définir des valeurs par défaut avec des paramètres par défaut de fonction
LastDeveloperCard.defaultProps = {
    developer: {
        // image: 'default_image_url.jpg',
        username: 'tony',
        // joinDate: 'Unknown',
        // description: 'No description provided.'
    }
};

export default LastDeveloperCard;
