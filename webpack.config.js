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
    // Ajouts des entrées
    // Jquery 
    .addEntry('jquery', './node_modules/jquery/dist/jquery.js')
    //jQuery UI
    .addEntry('jquery_ui', './node_modules/jquery-ui-dist/jquery-ui.js')
    .addStyleEntry('jquery_ui_theme', './node_modules/jquery-ui/dist/themes/blitzer/theme.css')
    .addStyleEntry('jquery_structure', './node_modules/jquery-ui-dist/jquery-ui.structure.css')
    // semantic-ui
    .addEntry('semantic', './semantic/dist/semantic.js')
    .addStyleEntry('semantic_css', './semantic/dist/semantic.css')
    // semantic-themes
    .addStyleEntry('semantic_less', './semantic/src/semantic.less')
    // general
    .addEntry('jquery_popup', './node_modules/jquery-popup-overlay/jquery.popupoverlay.js') // ou .scss pour Sass
    // bootstrap 
    .addEntry('bootstrap', './node_modules/bootstrap/dist/js/bootstrap.js')
    .addStyleEntry('bootstrap_css', './node_modules/bootstrap/dist/css/bootstrap.css')
    // uikit 
    .addEntry('uikit', './node_modules/uikit/dist/js/uikit.js')
    .addEntry('uikit-icons', './node_modules/uikit/dist/js/uikit-icons.js')
    .addStyleEntry('uikit_css', './node_modules/uikit/dist/css/uikit.css')
    // app
    .addEntry('app', './assets/app.js')
    // Message_flash
    .addEntry('messageFlash', './assets/js/alert/messageFlash.js') 
    // Navbar 
    .addEntry('ViewNavbar', './assets/js/navbar/ViewNavbar.js')
    // Search motor
    // .addEntry('ViewSearch', './assets/js/forms/ViewSearch.js')
    .addEntry('searchMotor', './assets/js/searchMotor/searchMotor.js')
    // Service add
    .addEntry('ViewServiceForm', './assets/js/forms/ViewServiceForm.js')
    // Dropdown navbar
    .addEntry('ViewNavbarDropdown', './assets/js/dropdown/ViewNavbarDropdown.js')
    // Slick-carousel
    .addEntry('slick-carousel', './node_modules/slick-carousel/slick/slick.js')
    .addStyleEntry('slick-carousel-css', './node_modules/slick-carousel/slick/slick.css')
    .addStyleEntry('slick-carousel-theme-css', './node_modules/slick-carousel/slick/slick-theme.css') // Optionnel
    .addEntry('HomeCarrousel', './assets/js/carousel/HomeCarousel.js') 
    // Preview image FileUpload
    .addEntry('previewImage', './assets/js/profile/previewImage.js') 
    // assets personnalisés
    .addStyleEntry('app_styles', [
        './assets/styles/sticky.css',
        './assets/styles/navbar.css',
        './assets/styles/dropdown.css',
        './assets/styles/login-register.css',
        './assets/styles/admin.css',
        './assets/styles/app.css',
        './assets/styles/scss_styles.scss'
    ])
    // REACT
    .enableReactPreset()
    // SticiyFooter React
    .addEntry('StickyFooter', './assets/js/components/StickyFooter.js')
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
        'window.jQuery': 'jquery'
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
        options.watchFiles = ['assets/**/*', 'semantic/src/**/*'];
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
    ;

module.exports = Encore.getWebpackConfig();