const Encore = require('@symfony/webpack-encore');
const path = require('path'); // Ajoutez cette ligne
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

    // Ajout des entrées pour jQuery et jQuery UI
    .addEntry('jquery', './node_modules/jquery/dist/jquery.js')
    .addEntry('jquery_ui', './node_modules/jquery-ui-dist/jquery-ui.js')
    .addStyleEntry('jquery_ui_theme', './node_modules/jquery-ui/dist/themes/redmond/theme.css')
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