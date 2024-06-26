/*******************************
 Build Docs
 *******************************/

const
    fs          = require('fs'),
    extend      = require('extend'),

    // node dependencies
    console     = require('@fomantic/better-console'),
    gulp        = require('gulp'),
    map         = require('map-stream'),

    // gulp dependencies
    print       = require('gulp-print').default,

    // user config
    configDocs      = require('../config/docs'),

    // install config
    tasks       = require('../config/tasks'),
    configSetup = require('../config/project/config'),
    install     = require('../config/project/install'),

    // metadata parsing
    metadata    = require('./metadata'),

    // build methods
    buildJS     = require('../build/javascript').buildJS,
    buildCSS    = require('../build/css').buildCSS,
    buildAssets = require('../build/assets').buildAssets,

    // shorthand
    log         = tasks.log
;

module.exports = function (callback) {
    // use a different config
    const config = extend(true, {}, configDocs);
    configSetup.addDerivedValues(config);

    // shorthand
    const globs  = config.globs;
    const output = config.paths.output;

    /* --------------
      Parse metadata
    --------------- */

    function buildMetaData() {
        // parse all *.html.eco in docs repo, data will end up in
        // metadata.result object.  Note this assumes that the docs
        // repository is present and in proper directory location as
        // specified by docs.json.
        console.info('Building Metadata');

        return gulp.src(config.paths.template.eco + globs.eco)
            .pipe(map(metadata.parser))
            .on('end', function () {
                fs.mkdirSync(output.metadata, { recursive: true });
                fs.writeFileSync(output.metadata + '/metadata.json', JSON.stringify(metadata.result, null, 2));
            })
        ;
    }

    /* --------------
      Copy Examples
    --------------- */

    function copyExample() {
        // copy src/ to server
        console.info('Copying examples');

        return gulp.src('examples/**/*.*', { encoding: false })
            .pipe(gulp.dest(output.examples))
            .pipe(print(log.created))
        ;
    }

    /* --------------
       Copy Source
    --------------- */

    function copyLess() {
        // copy src/ to server
        console.info('Copying LESS source');

        return gulp.src('src/**/*.*', { encoding: false })
            .pipe(gulp.dest(output.less))
            .pipe(print(log.created))
        ;
    }

    /* --------------
         Build
    --------------- */

    console.info('Building Fomantic for docs');

    if (!install.isSetup()) {
        console.error('Cannot build files. Run "gulp install" to set-up Fomantic');
        callback();

        return;
    }

    gulp.series(
        buildMetaData,
        copyExample,
        copyLess,
        (callback) => buildJS('docs', config, callback),
        (callback) => buildCSS('docs', config, {}, callback),
        (callback) => buildAssets(config, callback)
    )(callback);
};
