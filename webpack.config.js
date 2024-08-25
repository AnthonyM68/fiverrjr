const Encore = require('@symfony/webpack-encore');
const path = require('path');
const webpack = require('webpack'); // Ajout de l'importation de webpack
// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')/*
     * ENTRY CONFIG
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    // Ajax xios method
    // .addEntry('axios', './node_modules/axios/dist/axios.js') // Facultatif 
    // AJAX Fetch method
    .addEntry('ajax', './assets/js/ajax/postData.js')
    // Jquery 
    .addEntry('jquery', './node_modules/jquery/dist/jquery.js')
    // Jquery-popup
    .addEntry('jquery_popup', './node_modules/jquery-popup-overlay/jquery.popupoverlay.js')
    // jQuery UI
    .addStyleEntry('jquery_ui_css', './assets/styles/jquery-ui/jquery-ui.css')
    // Jquery theme
    .addStyleEntry('jquery_ui_theme', './assets/styles/jquery-ui/jquery-ui.theme.css')
    // Jquery structure
    .addStyleEntry('jquery_ui_structure', './assets/styles/jquery-ui/jquery-ui.structure.css')
    // Jquery-ui.js
    .addEntry('jquery_ui_js', './assets/js/jquery-ui/jquery-ui.js')

    // semantic-themes
    .addStyleEntry('semantic_css', './semantic/dist/semantic.css')
    // semantic-source-theme
    .addStyleEntry('semantic_less', './semantic/src/semantic.less')

    // bootstrap non utilisé
    // .addEntry('bootstrap', './node_modules/bootstrap/dist/js/bootstrap.js')
    // .addStyleEntry('bootstrap_css', './node_modules/bootstrap/dist/css/bootstrap.css')
    // uikit 

    .addStyleEntry('uikit_css', './node_modules/uikit/dist/css/uikit.css')
    // tarteaucitron
    .addStyleEntry('tarteaucitron_css', './node_modules/tarteaucitronjs/css/tarteaucitron.css')
    // Slick-carousel
    .addEntry('slick-carousel', './node_modules/slick-carousel/slick/slick.js')
    // Slick-carousel CSS
    .addStyleEntry('slick-carousel-css', './node_modules/slick-carousel/slick/slick.css')
    .addStyleEntry('slick-carousel-theme-css', './node_modules/slick-carousel/slick/slick-theme.css')
    // REACT ....Carousel 
    .addStyleEntry('react-responsive-carousel', './node_modules/react-responsive-carousel/lib/styles/carousel.min.css')
    .addStyleEntry('react-multi-carousel', './node_modules/react-multi-carousel/lib/styles.css')

    // Assets personnalisés
    .addStyleEntry('app_styles', [
        './assets/styles/alert.scss',
        './assets/styles/navbar.scss',
        './assets/styles/dropdown.scss',
        './assets/styles/parallax/parallax.scss',
        './assets/styles/parallax/parallax_home.scss',
        './assets/styles/parallax/parallax_footer.scss',
        './assets/styles/cards/userCard.scss',
        './assets/styles/cards/itemUser.scss',
        './assets/styles/cards/itemUserCards.scss',
        './assets/styles/banner/banner.scss',
        './assets/styles/homePage.scss',
        './assets/styles/profile.scss',
        './assets/styles/carouselComponent.scss',
        './assets/styles/developerSearch.scss',
        './assets/styles/sticky.css',
        './assets/styles/login-register.scss',
        './assets/styles/cart.scss',
        './assets/styles/footer.scss',
        './assets/styles/client.scss',
        './assets/styles/scss_styles.scss'
    ])
    // app
    .addEntry('app', './assets/app.js')
    // semantic-ui Framework
    .addEntry('semantic', './semantic/dist/semantic.js')

    // Styles et scripts de Semantic UI
    .addStyleEntry('semantic_ui_react_css', './node_modules/semantic-ui-css/semantic.min.css') // Assurez-vous que le chemin est correct
    .addEntry('semantic_ui_react_js', './node_modules/semantic-ui-css/semantic.min.js')

    // uikit Framework
    .addEntry('uikit', './node_modules/uikit/dist/js/uikit.js')
    .addEntry('uikit-icons', './node_modules/uikit/dist/js/uikit-icons.js')

    // Message_flash
    .addEntry('messageFlash', './assets/js/alert/messageFlash.js')
    // DisplayResults 
    .addEntry('displayResultsServices', './assets/js/search/services/displayResults.js')
    .addEntry('displayResultsUsers', './assets/js/search/users/displayResults.js')
    // Navbar 
    .addEntry('navbar', './assets/js/navbar/navbar.js')
    // Search motor
    .addEntry('searchMotor', './assets/js/searchMotor/searchMotor.js')
    // Dropdown navbar
    .addEntry('dropdown', './assets/js/dropdown/dropdown.js')
    // // Slick-carousel
    // .addEntry('slick-carousel', './node_modules/slick-carousel/slick/slick.js')
    // Preview image FileUpload
    .addEntry('previewImage', './assets/js/preview/previewImage.js')
    // Orders list pending / completed
    .addEntry('Order', './assets/js/profile/Order.js')
    // Service add
    .addEntry('Service', './assets/js/profile/Service.js')
    // User edit
    .addEntry('User', './assets/js/profile/User.js')
    // Cart
    // .addEntry('cart', './assets/js/cart/cart.js')
    .addEntry('home', './assets/js/home/home.js')
    // REACT,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
    .enableReactPreset()
    // useFetch ( effectue les requete ajax pour react)
    .addEntry('useFetch', './assets/js/useFetch.jsx')
    // Paralax
    .addEntry('Parallax', './assets/js/components/Parallax/Parallax.jsx')
    // UserCard
    .addEntry('UserCard', './assets/js/components/Card/UserCard.jsx')
    // UserCard
    .addEntry('ItemUser', './assets/js/components/Card/ItemUser.jsx')
    // ItemUserCards
    .addEntry('ItemUserCards', './assets/js/components/Card/ItemUserCards.jsx')

    // DeveloperSearch
    .addEntry('DeveloperSearch', './assets/js/components/DeveloperSearch/DeveloperSearch.jsx')
    // CarouselComponent React
    .addEntry('CarouselComponent', './assets/js/components/Carousel/CarouselComponent.jsx')
    // HomePage
    .addEntry('Homepage', './assets/js/components/HomePage/HomePage.jsx')
    // StickyFooter React
    .addEntry('StickyFooter', './assets/js/components/Sticky/StickyFooter.jsx')
    // active le code splitting
    // Divise en fragment et charge que lorsque nécessaire, gain performance
    .splitEntryChunks()
    // combine tous les runtime chunks en un seul
    // runtime charge et exécuter les modules et les combine, réduit le nombre de requête
    .enableSingleRuntimeChunk()
    // efface le répertoire de sortie (public/build/) avant une nouvelle compilation
    // cela évite les conflits et les problèmes de cache
    .cleanupOutputBeforeBuild()
    // active les notifications de comoilation
    .enableBuildNotifications()
    // les source maps permettent de mapper le code minifié ou transpilé à son code source original
    // A DESACTIVER EN PRODUCTION
    .enableSourceMaps(!Encore.isProduction())
    // active le versioning des fichiers en production
    .enableVersioning(Encore.isProduction())
    // traitement des fichiers Sass (.scss et .sass)
    .enableSassLoader()
    // traitement des fichiers Less (.less). 
    .enableLessLoader()
    // charge jQuery globalement dans tous les fichiers JavaScript 
    // pas d'importation explicite dans les fichiers js.
    .autoProvidejQuery()
    // ajoute le plugin ProvidePlugin pour rendre jQuery disponible globalement
    .addPlugin(new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        axio: 'axios'
    }))
    // configuration des options du serveur de développement
    .configureDevServerOptions(options => {
        // rechargement à chaud
        options.hot = true;
        options.liveReload = true;
        options.static = {
            // défini le dossier de sortie
            directory: path.join(__dirname, 'public')
        };
        options.client = {
            overlay: false,
            progress: false
        };
        // Surveillance du thème semantic (changement de styles)
        options.watchFiles = [
            'assets/**/*', // Votre répertoire d'actifs personnalisés
            // 'semantic_ui_react_3.0.0/src/**/*', // Sources de semantic-ui-react
            'semantic/src/**/*' // Sources de semantic-ui-css
        ];
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
    ;

module.exports = Encore.getWebpackConfig();