var gulp       = require('gulp');
var config = require('./gulpConfig.json');
var plugins  = require('gulp-load-plugins')(config.loadOpts);
require('gulp-task-loader')();

gulp.task( 'default', function() {
	plugins.runSequence(
		['scss', 'scripts', 'images:raster', 'images:vector'],
		'browser-sync'
	);
	gulp.watch('./assets/img/raw/**/*', ['images:raster', 'images:vector']);
	gulp.watch('./assets/scss/**/*.scss', ['scss']);
	gulp.watch(['./assets/js/vendor/*.js', './assets/js/custom/*.js'], ['scripts']);
});

gulp.task( 'scripts', function() {
	plugins.runSequence(
		['scripts:custom', 'scripts:vendor'],
		'scripts:app',
		plugins.browserSync.reload
	);
});
