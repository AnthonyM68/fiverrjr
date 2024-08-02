document.addEventListener('DOMContentLoaded', () => {
    console.log('=> ViewNavabarDropdown.js loaded');
    
    // Initialize dropdowns for desktop
    $('.ui.dropdown').dropdown({
        on: 'hover'
    });

    // Toggle button for mobile menu
    $('.ui.basic.icon.toggle.button').on('click', function() {
        $('.ui.vertical.accordion.menu').toggleClass('visible');
    });

    // Initialize accordion for mobile
    $('.ui.accordion').accordion();


});

