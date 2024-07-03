// Importation des modules nécessaires
import React from 'react';  // Importer React
import { createRoot } from 'react-dom/client'; // Nouvelle importation pour React 18
import StickyParent from './js/components/StickyFooter'; // On importe le composant StickyFooter personnalisé

import 'jquery'; // Importer jQuery (global)
import './styles/navbar.css'; // CSS Composants personnalisés
import './styles/dropdown.css'; // CSS Composants personnalisés
import './styles/login-register.css'; // CSS Composants personnalisés
import './styles/app.css'; // CSS Application global personnalisé

// Initialisation de createRoot
const rootElement = document.getElementById('root');
const root = createRoot(rootElement);

// Rendre le composant StickyParent
root.render(<StickyParent />);

document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM fully loaded and parsed: app.js');

  
  const inputImgs = document.querySelectorAll(".inputImg");
    const imgPreviews = document.querySelectorAll(".imgPreview");
    inputImgs.forEach(function(inputImg, index) {

        inputImg.addEventListener("change", function(event) {
            const file = event.target.files[0];
            const imgPreview = imgPreviews[index]; 

            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    imgPreview.src = reader.result;
                    imgPreview.classList.add("d-block");
                };
                
                reader.readAsDataURL(file);
            } else {
                imgPreview.src = "#";
                imgPreview.classList.remove("d-block");
            }
        });
    });
  // Admin filtres
  // $('.ui.vertical.fluid.menu .item').on('click', function () {
  //   // Récupérer l'attribut data-tab correspondant
  //   let tabId = $(this).data('tab');
  //   // Désactiver tous les segments
  //   $('.ui.tab.segment').removeClass('active');
  //   // Activer le segment correspondant
  //   $('.ui.tab.segment[data-tab="' + tabId + '"]').addClass('active');
  // });

});


