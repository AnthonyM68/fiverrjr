// app.jsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import Parallax from './js/components/Parallax/Parallax';
import BestServicesCarousel from './js/components/Carousel/CarouselComponent';
import UserCard from './js/components/Card/UserCard';
import useFetch from './path/to/useFetch';

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
    const { data: lastUser, error } = useFetch(`/api/last/${role}`);

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    if (!lastUser) {
        return <div>Loading...</div>;
    }

    return <UserCard user={lastUser} />;
};

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

    // Sélectionner tous les boutons de fermeture
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

    // Sélectionner tous les conteneurs de résultats de recherche
    const searchResultsContainers = document.querySelectorAll('.search-results-container');

    // Utilisation d'un exemple pour ouvrir un conteneur spécifique
    if (searchResultsContainers.length > 0) {
        openSearchResults(searchResultsContainers[0]);
    }

    openSearchResults;
});
