import React, { useState, useEffect } from 'react';
import { displayResults } from '../search/users/displayResults.js';
import { showAlert, clean } from './../alert/messageFlash.js';
import { postData } from './../ajax/postData.js';
import { createRoot } from 'react-dom/client';
import { HomePage } from './../../js/components/HomePage/HomePage'

document.addEventListener('DOMContentLoaded', function () {
    console.log("=> home.js loaded");

    const homePage = document.getElementById("homepage-root");
    if (homePage) {
        createRoot(homePage).render(<HomePage />);
    }

    // On sélectionne toutes les icônes de recherche utilisateur du home
    // const searchIcons = document.querySelectorAll('.ui.icon.input .search.icon.search-user');
    // // ajouter un écouteur d'événement à chaque icône de recherche
    // searchIcons.forEach((searchIcon) => {
    //     searchIcon.addEventListener('click', function (event) {
    //         handleSearchUser(event, searchIcon);
    //     });
    // });
});