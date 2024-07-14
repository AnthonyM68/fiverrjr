import React from 'react';
import ReactDOM from 'react-dom';
// import CarouselComponent from './js/components/CarouselComponent';
import { Parallax } from './js/components/Parallax';

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed: app.js');
    const carouselRoot = document.getElementById('carousel-root');
    /* if (carouselRoot) {
         ReactDOM.render(<CarouselComponent />, carouselRoot);
     }*/
    const parallaxRoot = document.getElementById('parallax-root');
    if (parallaxRoot) {
        const imageUrl = 'img/services/service.jpg'; // Remplacez par votre URL d'image
        ReactDOM.render(<Parallax imageUrl={imageUrl} />, parallaxRoot);
    }
});
