import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

import { Parallax, ParallaxDouble } from './js/components/Parallax';
import { BestServicesCarousel } from './js/components/carousel/CarouselComponent';
import UserCard from './js/components/UserCard';

// Exemple de données des meilleurs services
const bestServices = [
    {
        image: 'img/services/service.jpg',
        title: 'Service 1',
        username: 'User 1',
        description: 'Description du service 1',
        reviews: 34
    },
    // Ajoutez les autres services...
];

// Composant pour Parallax
const ParallaxComponent = () => {
    return <Parallax />;
};

// Composant pour le Carousel
const CarouselComponent = ({ services }) => {
    return <BestServicesCarousel services={services} />;
};

// Composant pour afficher le dernier utilisateur inscrit
const LastUser = ({ userType }) => {
    const [lastUser, setLastUser] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        console.log(`Fetching last ${userType}...`);
        fetch(`/api/last/${userType}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response Last${userType} was not ok ` + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data fetched:', data);
                setLastUser(data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                setError(error);
            });
    }, [userType]);

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    if (!lastUser) {
        return <div>Loading...</div>;
    }

    return <UserCard user={lastUser} />;
}

// Vérifier et rendre les composants en fonction des éléments DOM
document.addEventListener('DOMContentLoaded', () => {
    const parallaxRoot = document.getElementById('parallax-root');
    if (parallaxRoot) {
        const root = createRoot(parallaxRoot);
        root.render(<ParallaxComponent />);
    }

    const carouselRoot = document.getElementById('bestservices-root');
    if (carouselRoot) {
        const root = createRoot(carouselRoot);
        root.render(<CarouselComponent services={bestServices} />);
    }

    const lastDeveloperRoot = document.getElementById('last-developer-root');
    if (lastDeveloperRoot) {
        const root = createRoot(lastDeveloperRoot);
        root.render(<LastUser userType="ROLE_DEVELOPER" />);
    }
    const lastClientRoot = document.getElementById('last-client-root');
    if (lastClientRoot) {
        const root = createRoot(lastClientRoot);
        root.render(<LastUser userType="ROLE_CLIENT"  />);
    }
});
