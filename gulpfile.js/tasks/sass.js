// Include gulp
var gulp = require('gulp'),
	sassdoc = require('sassdoc'),
	del = require('del'),
	plugins = require('gulp-load-plugins')({ 
		camelize: true,
		pattern: ['gulp-*', 'gulp.*'],
  	replaceString: /\bgulp[\-.]/ 
	}),
	argv = require('yargs').argv,
	handleErrors = require('../lib/handleErrors'),
	CONFIG = require('../config/sass'); 

// Delete the dist directory
gulp.task('clean:css', function(cb) {
 	return del(CONFIG.PATHS.clean, cb);
});

// Delete the dist directory
gulp.task('clean:sassdoc', function() {
 	return del(SASSDOC_OPTIONS.dest);
});

gulp.task('sass', ['clean:css'], function(cb) {
 	return gulp.src(CONFIG.PATHS.sass, {cwd: CONFIG.BASES.src})
		.pipe(plugins.if(CONFIG.IS_DEV, plugins.sourcemaps.init()))
		.pipe(plugins.sass({
			sourceMap: 'scss',
			outputStyle: CONFIG.SASS_OUTPUT_STYLE
		}).on('error', handleErrors))
		.pipe(plugins.base64({ extensions:['svg'] }))
		.pipe(plugins.groupCssMediaQueries().on('error', handleErrors))
		.pipe(plugins.cssnano({
			autoprefixer: {
        add: true, 
				browsers: CONFIG.AUTOPREFIX_BROWSERS, // This tool is magic and you should use it in all your projects :)
				zindex: false
			}
		}).on('error', handleErrors))
		//.pipe(plugins.csscomb().on('error', handleErrors))
		.pipe(plugins.rename('style.css'))
		.pipe(plugins.if(CONFIG.IS_DEV, plugins.sourcemaps.write()))
		.pipe(gulp.dest(CONFIG.BASES.dist + 'css/'))
    .on("error", handleErrors);
});

gulp.task('sass:sassdoc', ['clean:sassdoc'], function () {
  return gulp.src(CONFIG.PATHS.sass, {cwd: CONFIG.BASES.src})
		.pipe(sassdoc({
			dest: CONFIG.BASES.dist+'sassdoc'
		}));
});

/*gulp.task('sass', [
	'sass:sassdoc', 
	'sass:scss'
]);*/