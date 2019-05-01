// Include gulp
var gulp = require('gulp'),
	del = require('del'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	argv = require('yargs').argv,
	handleErrors = require('../lib/handleErrors'),
	CONFIG = require('../config/images'); 

// Delete the dist directory
gulp.task('clean:images', function() {
 	return del(CONFIG.PATHS.clean);
});

gulp.task('images', ['clean:images'], function() {
  return gulp.src(CONFIG.PATHS.images, {cwd: CONFIG.BASES.src})
    .pipe(plugins.imagemin(CONFIG.IMGMIN_OPTIONS))
    .pipe(gulp.dest(CONFIG.BASES.dist + 'images/'))
    .pipe(plugins.size())
    .on("error", handleErrors);
});