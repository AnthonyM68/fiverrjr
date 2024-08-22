import 'jquery';
import './styles/navbar.css';
import './styles/dropdown.css';
import './styles/login-register.css';
import './styles/app.css';

document.addEventListener('DOMContentLoaded', function () {

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
  $('.ui.dropdown').dropdown({
    on: 'hover',
    action: 'nothing',
    preserveHTML: true,
    action: function (text, value, element) {
      if ($(element).hasClass('dropdown')) {
        return false;
      }
    },
  });

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
   * Search motor 
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


