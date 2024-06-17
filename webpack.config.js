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
    .addEntry('jquery_js', './assets/styles/jquery.min.js')

    // jquery-ui
    .addEntry('jquery_ui_js', './assets/styles/jquery-ui.min.js')
    .addStyleEntry('jquery_ui_css', './assets/styles/jquery-ui.min.css')

    // // semantic-ui
    .addStyleEntry('semantic_css', './semantic/dist/semantic.css')
    // // semantic-themes
    .addStyleEntry('semantic_less', './semantic/src/semantic.less')
    .addEntry('semantic_js', './semantic/dist/semantic.js')

    // // bootstrap 
    .addEntry('bootstrap_js', './assets/styles/bootstrap.min.js')
    .addStyleEntry('bootstrap_css', './assets/styles/bootstrap.min.css')

    // // uikit 
    .addEntry('uikit_js', './assets/styles/uikit.min.js')
    .addStyleEntry('uikit_css', './node_modules/uikit/dist/css/uikit.min.css')

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