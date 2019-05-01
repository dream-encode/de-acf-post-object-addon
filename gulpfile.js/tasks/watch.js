// Include gulp
var gulp = require('gulp'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	argv = require('yargs').argv,
	handleErrors = require('../lib/handleErrors'),
	CONFIG  = require('../config/watch');

// Watch Files For Changes
gulp.task('watch', function(cb) {
  console.log('Watching for changes...');

	gulp.watch(CONFIG.PATHS.scripts, { cwd: CONFIG.BASES.src }, ['scripts']);
	gulp.watch(CONFIG.PATHS.sass, { cwd: CONFIG.BASES.src }, ['sass']);
	gulp.watch(CONFIG.PATHS.images, { cwd: CONFIG.BASES.src }, ['images']);
	gulp.watch(CONFIG.PATHS.fonts, { cwd: CONFIG.BASES.src }, ['fonts']);
	gulp.watch(CONFIG.PATHS.vendor, { cwd: CONFIG.BASES.src }, ['vendor']);
});