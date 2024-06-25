
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'jquery';
import './styles/navbar.css';
import './styles/dropdown.css';
import './styles/login-register.css';
import './styles/app.css';

document.addEventListener('DOMContentLoaded', function () {

  $('.ui.dropdown').dropdown({
    on: 'hover',
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