
// Include gulp
var gulp = require('gulp'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	argv = require('yargs').argv,
	handleErrors = require('../lib/handleErrors'),
	CONFIG = require('../config/modernizr'); 

//Add modernizr
gulp.task('modernizr', function(cb) {
	return gulp.src(CONFIG.PATHS.scripts, {cwd: CONFIG.BASES.src})
  	.pipe(plugins.if(CONFIG.IS_DEV, plugins.sourcemaps.init()))
    .pipe(plugins.modernizr())
    .pipe(plugins.if(CONFIG.IS_DEV, stripDebug()))
    .pipe(plugins.if(CONFIG.IS_DEV, uglify()))
  	.pipe(plugins.if(!CONFIG.IS_DEV, plugins.sourcemaps.write()))
		.pipe(gulp.dest(CONFIG.BASES.dist + 'js/'))
    .on("error", handleErrors);
});