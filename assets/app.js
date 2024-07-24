import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

import { Parallax, ParallaxDouble } from './js/components/Parallax/Parallax';
import { BestServicesCarousel } from './js/components/Carousel/CarouselComponent';
import UserCard from './js/components/Card/UserCard';

// import 'tarteaucitronjs/tarteaucitron';

const CarouselComponent = () => {
    const { data: bestServices, error } = useFetch(`/service/bestServices`);

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    if (!bestServices) {
        return <div>Loading...</div>;
    }

    return <BestServicesCarousel services={bestServices} />;
};

// const bestServices = [
//     {
//         image: 'img/services/service.jpg',
//         title: 'Service 1',
//         username: 'User 1',
//         description: 'Description du service 1',
//         reviews: 34
//     },
//     {
//         image: 'img/services/service.jpg',
//         title: 'Service 2',
//         username: 'User 2',
//         description: 'Description du service 2',
//         reviews: 21
//     },
//     {
//         image: 'img/services/service.jpg',
//         title: 'Service 3',
//         username: 'User 3',
//         description: 'Description du service 3',
//         reviews: 18
//     },
//     {
//         image: 'img/services/service.jpg',
//         title: 'Service 4',
//         username: 'User 4',
//         description: 'Description du service 4',
//         reviews: 45
//     },
//     {
//         image: 'img/services/service.jpg',
//         title: 'Service 5',
//         username: 'User 5',
//         description: 'Description du service 5',
//         reviews: 67
//     }
// ];


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

    // const carouselRoot = document.getElementById('bestservices-root');
    // if (carouselRoot) {
    //     const root = createRoot(carouselRoot);
    //     root.render(<CarouselComponent />);
    // }
});
