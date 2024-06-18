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

    // jquery
    .addEntry('jquery', './assets/styles/jquery.min.js')

    // jquery-ui
    .addEntry('jquery_ui', './assets/styles/jquery-ui.min.js')
    .addStyleEntry('jquery_ui_css', './assets/styles/jquery-ui.min.css')

    // semantic-ui
    .addEntry('semantic', './semantic/dist/semantic.min.js')
    .addStyleEntry('semantic_css', './semantic/dist/semantic.min.css')
    // semantic-themes
    .addStyleEntry('semantic_less', './semantic/src/semantic.less')
    // general
    // .addEntry('jquery_popup', './assets/styles/jquery.popup.minified.js') // ou .scss pour Sass


    // bootstrap 
    .addEntry('bootstrap', './assets/styles/bootstrap.min.js')
    .addStyleEntry('bootstrap_css', './assets/styles/bootstrap.min.css')

    // uikit 
    .addEntry('uikit', './assets/styles/uikit.js')
    .addEntry('uikit-icons', './assets/styles/uikit-icons.js')
    .addStyleEntry('uikit_css', './assets/styles/uikit.min.css')
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