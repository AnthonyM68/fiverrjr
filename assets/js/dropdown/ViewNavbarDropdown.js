/*********************************************************
 * Dropdonw Theme Navbar
//  */

// Configuration du dropdown navbar Theme Category Course
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed: ViewNavabarDropdown.js');

    // let dropdowns = document.querySelectorAll('.item.dropdown');

    // dropdowns.forEach(function(dropdown) {
    //     // Add event listener for mouse hover
    //     dropdown.addEventListener('mouseenter', function() {
    //         let menu = dropdown.querySelector('.menu');
    //         if (menu) {
    //             menu.style.display = 'block';
    //         }
    //     });

    //     dropdown.addEventListener('mouseleave', function() {
    //         let menu = dropdown.querySelector('.menu');
    //         if (menu) {
    //             menu.style.display = 'none';
    //         }
    //     });

    //     // Optional: Add event listener for click if needed
    //     dropdown.addEventListener('click', function(event) {
    //         let menu = dropdown.querySelector('.menu');
    //         if (menu) {
    //             if (menu.style.display === 'block') {
    //                 menu.style.display = 'none';
    //             } else {
    //                 menu.style.display = 'block';
    //             }
    //             event.preventDefault(); // Prevent default action
    //         }
    //     });
    // });
    // Configuration du dropdown navbar Theme Category Course
<<<<<<< HEAD
    document.addEventListener('DOMContentLoaded', () => {
        console.log('=> ViewNavabarDropdown.js loaded!');
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
        /*const observer = new MutationObserver(function (mutationsList) {
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
=======
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
    /*const observer = new MutationObserver(function (mutationsList) {
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
>>>>>>> ab4038126793de0d041a51225717c263819f881d
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
    }*/
});
