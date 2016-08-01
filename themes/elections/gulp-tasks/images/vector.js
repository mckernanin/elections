var gulp       = require('gulp');
var root       = process.cwd();
var config     = require( root + '/gulpConfig.json');
var plugins    = require('gulp-load-plugins')(config.loadOpts);
module.exports = function() {
	return gulp.src( config.vector.files )
		.pipe( plugins.rimraf({ force: true }))
		.pipe( plugins.svgmin({
				plugins: [{
						removeViewBox: false
				}, {
						removeUselessStrokeAndFill: false
				}]
		}))
		.pipe( gulp.dest( config.vector.dest ) );
}
