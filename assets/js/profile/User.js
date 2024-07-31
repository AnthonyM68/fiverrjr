import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
// import du component
import UserCard from './../../js/components/Card/UserCard';

document.addEventListener('DOMContentLoaded', () => {
    console.log('=> User.js loaded!');
    /**
     * REACT dernier développeur affichage de la carte dans le profil utilisateur
     */
    const lastDeveloperRoot = document.getElementById('last-developer-profile');
    if (lastDeveloperRoot) {
        const root = createRoot(lastDeveloperRoot);
        // on récupère les données du dernier utilisateur depuis le template user/index.html.twig
        root.render(<UserCard user={window.__INITIAL_DATA__.lastDeveloper} />);
    }
    /**
     * REACT dernier client affichage de la carte dans le profil utilisateur
     */
    const lastClientRoot = document.getElementById('last-client-profile');
    if (lastClientRoot) {
        const root = createRoot(lastClientRoot);
        // on récupère les données du dernier utilisateur depuis le template user/index.html.twig
        root.render(<UserCard user={window.__INITIAL_DATA__.lastClient} />);
    }

});