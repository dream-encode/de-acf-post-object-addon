// Include gulp
var gulp = require('gulp'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	argv = require('yargs').argv,
	handleErrors = require('../lib/handleErrors'),
	CONFIG = require('../config/bump'); 

gulp.task('bump', function(cb){
	return gulp.src(CONFIG.sourceDirectory + '/' + CONFIG.PATHS.package)
		.pipe(plugins.if(argv.major, plugins.bump({type: 'major'})))
		.pipe(plugins.if(argv.minor, plugins.bump({type: 'minor'})))
		.pipe(plugins.if(argv.patch, plugins.bump({type: 'patch'})))
		.pipe(plugins.if(argv.prerelease, plugins.bump({type: 'prerelease'})))
		.pipe(gulp.dest('./'))
    .on("error", handleErrors);
});