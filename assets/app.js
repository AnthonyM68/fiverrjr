// Importation des modules nécessaires
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
  // Initialiser le modal
  $('.ui.modal').modal('show');

  console.log('app.js');
  
  // Intercepter la soumission du formulaire (Service-search-motor ou Them-search-motor) SearchController
  const forms = document.querySelectorAll('.ajax-form');
  forms.forEach(form => {
    
    form.addEventListener('submit', function (event) {
      event.preventDefault(); // Empêche le rechargement de la page
      // Récupère les données du formulaire
      const formData = new FormData(this);
      const actionUrl = this.getAttribute('action');
      // Envoie les données du formulaire via Fetch API
      fetch(actionUrl, {
        method: 'POST',
        body: formData,
      })
        .then(response => {
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          if (data.error) {
            console.error('Error from server:', data.error);
            document.getElementById('search-results').innerHTML = '<p class="error">An error occurred: ' + data.error + '</p>';
            return;
          }
          let resultsHtml = '';
          if (data.results.empty) {
            resultsHtml += '<h2>Aucun résultats</h2>';
            document.getElementById('search-results').innerHTML = resultsHtml;
            // Si pas de réultats on quitte
            return;
          }
          // Mise à jour du contenu avec les résultats
          if (data.submitted_form === 'form_service' && data.results.service) {
            alert();
            resultsHtml += '<h3>Résultats pour Service</h3><div class="ui divided items">';
            data.results.service.forEach(service => {
              console.log(service);
              resultsHtml += `
               <div class="item">
                 <div class="image">
                   <img src="${service.picture}">
                 </div>
                 <div class="content">
                   <a class="header">${service.title}</a>
                   <div class="meta">
                     <span class="cinema">Union Square 14</span>
                   </div>
                   <div class="description">
                     <p>${service.description}</p>
                   </div>
                   <div class="extra">
                     <div class="ui label">IMAX</div>
                     <div class="ui label"><i class="globe icon"></i> Additional Languages</div>
                   </div>
                 </div>
               </div>`;
            });
            resultsHtml += '</div>';
          }

          if (data.submitted_form === 'form_theme' && data.results.theme) {
            resultsHtml += '<h2>Résultats pour Thème</h2><ul>';
            data.results.theme.forEach(theme => {
              resultsHtml += `<li>${theme.nameTheme}</li>`;
            });
            resultsHtml += '</ul>';
          }
          document.getElementById('search-results').innerHTML = resultsHtml;
        })
        .catch(error => {
          console.error('Erreur lors de la soumission du formulaire:', error);
          document.getElementById('search-results').innerHTML = '<p class="error">An error occurred: ' + error.message + '</p>';
        });
    });
  });


  // Admin filtres
  $('.ui.vertical.fluid.menu .item').on('click', function () {
    // Récupérer l'attribut data-tab correspondant
    let tabId = $(this).data('tab');
    // Désactiver tous les segments
    $('.ui.tab.segment').removeClass('active');
    // Activer le segment correspondant
    $('.ui.tab.segment[data-tab="' + tabId + '"]').addClass('active');
  });

  /*********************************************************
   * Dropdonw
   */
  // Configuration du dropdown navbar Theme Category Course
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

  const observer = new MutationObserver(function (mutationsList) {
    for (let mutation of mutationsList) {
      if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
        adjustAllMenuHeights();
        // fixSubMenuPositions(); 
        break;
      } else if (mutation.type === 'childList' && mutation.removedNodes.length > 0) {
        adjustAllMenuHeights();
        // fixSubMenuPositions(); 
        break;
      }
    }
  });
  const container = document.querySelector('.ui.fixed.borderless.huge.menu');
  observer.observe(container, { childList: true, subtree: true });

  function adjustAllMenuHeights() {
    const allMenus = document.querySelectorAll('.ui.dropdown.item > .menu');
    let maxHeight = 0;

    allMenus.forEach(menu => {
      const menuHeight = getMenuAndSubmenusHeight(menu);
      if (menuHeight > maxHeight) {
        maxHeight = menuHeight;
      }
    });

    allMenus.forEach(menu => {
      menu.style.height = maxHeight + 'px';
      const subMenus = menu.querySelectorAll('.item > .menu');
      subMenus.forEach(subMenu => {
        subMenu.style.height = maxHeight + 'px';
      });
    });
  }

  function getMenuAndSubmenusHeight(menu) {
    let menuHeight = menu.scrollHeight;
    const subMenus = menu.querySelectorAll('.item > .menu');
    subMenus.forEach(subMenu => {
      const subMenuHeight = subMenu.scrollHeight;
      if (subMenuHeight > menuHeight) {
        menuHeight = subMenuHeight;
      }
    });
    return menuHeight;
  }

  function fixSubMenuPositions() {
    const subMenus = document.querySelectorAll('.ui.dropdown.item > .menu > .item > .menu');
    subMenus.forEach(subMenu => {
      const parentMenu = subMenu.closest('.ui.dropdown.item > .menu');
      const parentOffset = parentMenu.getBoundingClientRect();
      subMenu.style.top = parentOffset.top + 'px';
    });
  }

  /**********************************************************************************
   * Search motor Ative Link after submit UI UX
   */
  // Sélectionnez tous les éléments de menu
  const menuItems = document.querySelectorAll('.ui.vertical.fluid.menu .item.field');
  // Ajoutez un gestionnaire de clic à chaque élément de menu
  menuItems.forEach(item => {
    item.addEventListener('click', function () {
      // Supprimez la classe 'active teal' de tous les éléments de menu
      menuItems.forEach(menu => menu.classList.remove('active', 'teal'));
      // Ajoutez la classe 'active teal' à l'élément cliqué
      this.classList.add('active', 'teal');
    });
  });
});


