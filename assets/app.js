
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/navbar.css';
import './styles/login-register.css';

document.addEventListener('DOMContentLoaded', function () {

  $('.ui.dropdown').dropdown({
    on: 'click',
    action: 'nothing',
    preserveHTML: true,
    action: function (text, value, element) {

      if ($(element).hasClass('dropdown')) {
        return false;
      }
    }
  });
});


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');