// Include gulp
var gulp = require('gulp'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	argv = require('yargs').argv,
	del = require('del'),
	handleErrors = require('../lib/handleErrors'),
	CONFIG = require('../config/vendor'); 

// Delete the dist directory
gulp.task('clean:vendor', function() {
 	return del(CONFIG.PATHS.clean);
});

gulp.task('bower', function() {
  return plugins.bower({ cmd: 'update'})
    .on("error", handleErrors);
});

gulp.task('vendor', ['clean:vendor'], function() {
  return gulp.src(CONFIG.PATHS.vendor)
  	//.pipe(plugins.sourcemaps.init())
    //.pipe(plugins.concat('vendor.js'))
    //.pipe(plugins.uglify())
  	//.pipe(plugins.sourcemaps.write())
    .pipe(gulp.dest(CONFIG.BASES.dist + 'vendor/'))
    .on("error", handleErrors);	
});