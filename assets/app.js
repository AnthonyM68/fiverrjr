
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
// import './styles/navbar.css';
import './styles/login-register.css';


document.addEventListener('DOMContentLoaded', function () {

  // $('.ui.tabular.menu .item').tab();

  let computerDropdownItems = document.querySelectorAll('.computer.only .dropdown.item');

  computerDropdownItems.forEach(function (item) {
    item.addEventListener('mouseover', function () {
      let popupOptions = {
        inline: true,
        hoverable: true,
        position: 'bottom left',
        delay: {
          show: 300,
          hide: 800
        }
      };
      // Logique pour afficher le popup
    });
  });

  let uiDropdowns = document.querySelectorAll('.ui.dropdown');

  uiDropdowns.forEach(function (dropdown) {
    dropdown.addEventListener('click', function () {
      let menu = dropdown.querySelector('.menu');
      menu.classList.toggle('visible');
    });
  });

  let uiAccordions = document.querySelectorAll('.ui.accordion > .title');

  uiAccordions.forEach(function (title) {
    title.addEventListener('click', function () {
      let content = title.nextElementSibling;
      content.classList.toggle('active');
    });
  });

  let toggleButton = document.querySelector('.ui.toggle.button');
  let verticalMenu = document.querySelector('.ui.vertical.menu');

  toggleButton.addEventListener('click', function () {
    verticalMenu.classList.toggle('visible');
    console.log('verticalMenu');
  });



});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');