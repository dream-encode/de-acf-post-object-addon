// Include gulp
var gulp = require('gulp'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	del = require('del'),
	argv = require('yargs').argv,
	handleErrors = require('../lib/handleErrors'),
	CONFIG = require('../config/scripts'); 

// Delete the dist directory
gulp.task('clean:scripts', function(cb) {
 	return del(CONFIG.PATHS.clean);
});

// Process scripts
gulp.task('scripts', ['clean:scripts'], function(cb) {	
	return gulp.src(CONFIG.PATHS.scripts, {cwd: CONFIG.BASES.src})
  	.pipe(plugins.if(CONFIG.IS_DEV, plugins.sourcemaps.init()))
		.pipe(plugins.jshint({elision: true}))
    .pipe(plugins.jshint.reporter('jshint-stylish'))
		.pipe(plugins.if(!CONFIG.IS_DEV, plugins.stripDebug()))
    .pipe(plugins.uglify().on("error", handleErrors))
  	.pipe(plugins.if(CONFIG.IS_DEV, plugins.sourcemaps.write()))
		.pipe(gulp.dest(CONFIG.BASES.dist + 'js/'))
    .on("error", handleErrors);
});