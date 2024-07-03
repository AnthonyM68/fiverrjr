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

    .setPublicPath('/build')
    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */

    // Ajouts des entrées

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
    // .addEntry('jquery_popup', './assets/styles/jquery.popup.minified.js') // ou .scss pour Sass
    // bootstrap 
    .addEntry('bootstrap', './node_modules/bootstrap/dist/js/bootstrap.js')
    .addStyleEntry('bootstrap_css', './node_modules/bootstrap/dist/css/bootstrap.css')

    // uikit 
    .addEntry('uikit', './node_modules/uikit/dist/js/uikit.js')
    .addEntry('uikit-icons', './node_modules/uikit/dist/js/uikit-icons.js')
    .addStyleEntry('uikit_css', './node_modules/uikit/dist/css/uikit.css')
    // app
    .addEntry('app', './assets/app.js')
    .addEntry('ViewSearch', './assets/js/forms/ViewSearch.js')
    // assets personnalisés
    .addStyleEntry('app_styles', [
        './assets/styles/navbar.css',
        './assets/styles/dropdown.css',
        './assets/styles/login-register.css',
        './assets/styles/admin.css',
        './assets/styles/app.css',
        './assets/styles/scss_styles.scss'
    ])
    // REACT
    // .enableReactPreset()
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .enableLessLoader()
    .autoProvidejQuery()
    .enableBuildNotifications()
    
    .addPlugin(new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery'
    }))
    
    .configureDevServerOptions(options => {
        options.hot = true;
        options.liveReload = true;
        options.static = {
            directory: path.join(__dirname, 'public')
        };
        options.client = {
            overlay: false,
            progress: false
        };
        options.watchFiles = ['assets/**/*', 'semantic/src/**/*'];
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
    ;

module.exports = Encore.getWebpackConfig();