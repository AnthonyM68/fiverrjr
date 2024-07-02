// // Importation des modules nécessaires
// import React from 'react';  // Importer React
// import { createRoot } from 'react-dom/client'; // Nouvelle importation pour React 18
// import StickyParent from './js/components/StickyFooter'; // On importe le composant StickyFooter personnalisé

import 'jquery'; // Importer jQuery (global)
import './styles/navbar.css'; // CSS Composants personnalisés
import './styles/dropdown.css'; // CSS Composants personnalisés
import './styles/login-register.css'; // CSS Composants personnalisés
import './styles/app.css'; // CSS Application global personnalisé

// Initialisation de createRoot
// const rootElement = document.getElementById('root');
// const root = createRoot(rootElement);

// // Rendre le composant StickyParent
// root.render(<StickyParent />);

document.addEventListener('DOMContentLoaded', function () {
  console.log("app_test.js");
  // Initialiser le modal de recherches avancées
  $('.ui.basic.modal').modal('show');

  // $('.ui.dropdown').dropdown({
  //   on: 'hover',
  //   action: 'nothing',
  //   preserveHTML: true,
  //   action: function (text, value, element) {
  //     if ($(element).hasClass('dropdown')) {
  //       return false;
  //     }
  //   },
  // });
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