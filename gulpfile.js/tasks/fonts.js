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
	CONFIG = require('../config/fonts'); 

// Delete the dist directory
gulp.task('clean:fonts', function() {
 	return del(CONFIG.PATHS.clean);
});

gulp.task('fonts', ['clean:fonts'], function() {
  return gulp.src(CONFIG.PATHS.fonts, {cwd: CONFIG.BASES.src})
    .pipe(gulp.dest(CONFIG.BASES.dist + 'fonts/'))
    .on("error", handleErrors);
});