import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

import { Parallax, ParallaxDouble } from './js/components/Parallax';
import { BestServicesCarousel } from './js/components/carousel/CarouselComponent';
import UserCard from './js/components/UserCard';





// Composant pour Parallax
const ParallaxComponent = () => {
    return <Parallax />;
};

// Composant pour le Carousel
const CarouselComponent = ({ services }) => {
    return <BestServicesCarousel services={services} />;
};

// Composant pour afficher le dernier utilisateur inscrit
const LastUser = ({ role }) => {
    const [lastUser, setLastUser] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        console.log(`Fetching last ${role}...`);
        fetch(`/api/last/${role}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response Last${role} was not ok ` + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data fetched last User:', data);
                setLastUser(data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                setError(error);
            });
    }, [role]);

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
        root.render(<LastUser role="ROLE_DEVELOPER" />);
    }
    const lastClientRoot = document.getElementById('last-client-root');
    if (lastClientRoot) {
        const root = createRoot(lastClientRoot);
        root.render(<LastUser role="ROLE_CLIENT" />);
    }






    // Sélectionne tous les boutons de fermeture
    const closeButtons = document.querySelectorAll('.close-results');

    closeButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const container = event.target.closest('.search-results-container');
            if (container) {
                container.style.display = 'none';
            }
        });
    });

    // Exemple d'ouverture du conteneur (à appeler lorsque vous voulez afficher les résultats)
    const openSearchResults = (container) => {
        container.style.display = 'block';
    };

    // Sélectionne tous les conteneurs de résultats de recherche
    const searchResultsContainers = document.querySelectorAll('.search-results-container');

    // Utilisation d'un exemple pour ouvrir un conteneur spécifique
    // Par exemple, pour ouvrir le premier conteneur :
    if (searchResultsContainers.length > 0) {
        openSearchResults(searchResultsContainers[0]);
    }

    openSearchResults;
});
