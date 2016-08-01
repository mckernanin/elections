var gulp       = require('gulp');
var root       = process.cwd();
var config     = require( root + '/gulpConfig.json');
var plugins    = require('gulp-load-plugins')(config.loadOpts);
module.exports = function() {
	return gulp.src( config.js.vendor.files )
		.pipe( plugins.concat( config.js.vendor.name + '.js' ))
		.pipe( gulp.dest( config.js.vendor.dest ))
		.pipe( plugins.rename( {
			basename: config.js.vendor.name,
			suffix: '.min'
		}))
		.pipe( plugins.uglify() )
		.pipe( gulp.dest( config.js.vendor.dest ) )
		.pipe( plugins.notify({ message: 'Vendor Scripts Complete', onLast: true }));
}
