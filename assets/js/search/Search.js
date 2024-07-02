
//Search motor Ative Link after submit UI UX
(function () {
    document.addEventListener('DOMContentLoaded', () => {
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
})();