import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

import { Parallax, ParallaxDouble } from './js/components/Parallax/Parallax';
import { BestServicesCarousel } from './js/components/Carousel/CarouselComponent';
import UserCard from './js/components/Card/UserCard';
// hook useFetch
import useFetch from './js/useFetch';
import config from './js/config'

// Composant pour Parallax
const ParallaxComponent = () => {
    return <Parallax />;
};

const CarouselComponent = () => {
    const { data: bestServices, error } = useFetch(`/service/bestServices`);

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    if (!bestServices) {
        return <div>Loading...</div>;
    }
    console.log(bestServices);

    return <BestServicesCarousel services={bestServices} />;
};

// Composant pour afficher le dernier utilisateur inscrit
const LastUser = ({ role }) => {
    const { data: lastUser, error } = useFetch(`/last/user/${role}`);

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
    console.log('=> app.js loaded');
    fetch('/cart/totalItemFromCart')
        .then(response => {
            // Vérification du statut de la réponse
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Conversion de la réponse en JSON
        })
        .then(data => {
            console.log(data['totalServiceItem']);
            //document.getElementById('cart-icon').innerText = data['totalServiceItem'];

        })
        .catch(error => {
            throw new Error(error.message);
        });

    const parallaxRoot = document.getElementById('parallax-root');
    if (parallaxRoot) {
        const root = createRoot(parallaxRoot);
        root.render(<ParallaxComponent />);
    }
    const carouselRoot = document.getElementById('bestservices-root');
    if (carouselRoot) {
        const root = createRoot(carouselRoot);
        root.render(<CarouselComponent />);
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
});
