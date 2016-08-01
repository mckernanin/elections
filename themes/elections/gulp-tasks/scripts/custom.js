var gulp       = require('gulp');
var root       = process.cwd();
var config     = require( root + '/gulpConfig.json');
var plugins    = require('gulp-load-plugins')(config.loadOpts);
module.exports = function() {
	return gulp.src( config.js.custom.files )
		.pipe( plugins.plumber() )
		.pipe( plugins.concat( config.js.custom.name + '.js' ))
		.pipe( plugins.concatUtil.header('jQuery(function($){\n$(document).ready(function() {\n'))
		.pipe( plugins.concatUtil.footer('\n});\n});'))
		.pipe( gulp.dest( config.js.custom.dest ))
		.pipe( plugins.rename( {
			basename: config.js.custom.name,
			suffix: '.min'
		}))
		.pipe( plugins.uglify() )
		.pipe( gulp.dest( config.js.custom.dest ) )
		.pipe( plugins.notify({ message: 'Custom Scripts Complete', onLast: true }));
}
