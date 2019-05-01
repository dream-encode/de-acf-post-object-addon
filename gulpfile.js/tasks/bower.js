// Include gulp
var gulp = require('gulp'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	argv = require('yargs').argv,
	handleErrors = require('../lib/handleErrors'),
	CONFIG = require('../config/bower'); 

gulp.task('bower', ['clean:vendor'], function() {
  return plugins.bower({ cmd: 'update'})
    .on("error", handleErrors);
});