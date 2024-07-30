import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

// import { Parallax, ParallaxDouble } from './js/components/Parallax/Parallax';
import { BestServicesCarousel } from './js/components/Carousel/CarouselComponent';



import StickyFooter from './js/components/Sticky/StickyFooter'
// hook useFetch
import useFetch from './js/useFetch';

// const StickyParentComponent = () => {
//     return <StickyParent />;
// };

// const CarouselComponent = () => {
//     const { data: bestServices, error } = useFetch(`/service/bestServices`);

//     if (error) {
//         return <div>Error: {error.message}</div>;
//     }
//     if (!bestServices) {
//         return <div className="ui active inline loader"></div>
//     }
//     return <BestServicesCarousel services={bestServices} />;
// };

// Composant pour afficher le dernier utilisateur inscrit
const LastUser = ({ role }) => {
    const { data: lastUser, error } = useFetch(`/last/user/${role}`);

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    if (!lastUser) {
        return <div className="ui segment">
        <div className="ui active inverted dimmer">
          <div className="ui text loader">Loading</div>
        </div>
        <p></p>
      </div>;
    }

    return <UserCard user={lastUser} />;
};


// Vérifier et rendre les composants en fonction des éléments DOM
document.addEventListener('DOMContentLoaded', () => {
    console.log('=> app.js loaded');

    const carouselRoot = document.getElementById('bestservices-root');
    if (carouselRoot) {
        const root = createRoot(carouselRoot);
        root.render(<BestServicesCarousel />);
    }

    // const lastDevlRoot = document.getElementById('last-developers');
    // if (lastDevlRoot) {
    //     const root = createRoot(lastDevlRoot);
    //     root.render(<CarouselComponent />);
    // }


    // Profile

    // const lastDeveloperRoot = document.getElementById('last-developer-root');
    // if (lastDeveloperRoot) {
    //     const root = createRoot(lastDeveloperRoot);
    //     root.render(<LastUser role="ROLE_DEVELOPER" />);
    // }

    // const lastClientRoot = document.getElementById('last-client-root');
    // if (lastClientRoot) {
    //     const root = createRoot(lastClientRoot);
    //     root.render(<LastUser role="ROLE_CLIENT" />);
    // }

    // const segmentRoot = document.getElementById('segment-root');
    // if (segmentRoot) {
    //     const root = createRoot(segmentRoot);
    //     root.render(<SegmentCompo />);
    // }
    // const segmentRoot2 = document.getElementById('segment-root-2');
    // if (segmentRoot2) {
    //     const root = createRoot(segmentRoot2);
    //     root.render(<SegmentCompo />);
    // }
    // const segmentRoot3 = document.getElementById('segment-root-3');
    // if (segmentRoot3) {
    //     const root = createRoot(segmentRoot3);
    //     root.render(<SegmentCompo />);
    // }
    // const homeRoot = document.getElementById('homepage-root');
    // if (homeRoot) {
    //     const root = createRoot(homeRoot);
    //     root.render(<HomePage />);
    // }

    // const stickyRoot = document.getElementById('sticky-root');
    // if (stickyRoot) {
    //     const root = createRoot(stickyRoot);
    //     root.render(<StickyParentComponent />);
    // }

});
