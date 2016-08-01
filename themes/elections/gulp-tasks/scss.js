var gulp       = require('gulp');
var root       = process.cwd();
var config     = require( root + '/gulpConfig.json');
var plugins    = require('gulp-load-plugins')(config.loadOpts);
module.exports = function() {
	return gulp.src( config.scss.files )
		.pipe( plugins.plumber() )
		.pipe( plugins.sourcemaps.init() )
		.pipe( plugins.sass( {
			errLogToConsole : config.scss.errors,
			outputStyle : config.scss.output
		}))
		.pipe( plugins.sourcemaps.write() )
		.pipe( plugins.autoprefixer( {
			browsers : [ 'last 2 versions' ],
			cascade  : false
		}))
		.pipe( gulp.dest( config.scss.dest ) )
		.pipe( plugins.browserSync.stream() )
		.pipe( plugins.notify({ message: 'Styles task complete', onLast: true }));
}
