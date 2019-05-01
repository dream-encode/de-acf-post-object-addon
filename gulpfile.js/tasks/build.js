var gulp = require('gulp'),
	sequence = require('gulp-sequence'),
	argv = require('yargs').argv;
	
require('gulp-stats')(gulp);

gulp.task('build', function(cb) {
  sequence(['fonts', 'images', 'vendor'], ['sass', 'scripts'], ['watch'], cb);
});

//gulp.task('build', ['fonts', 'images', 'vendor', 'sass', 'scripts', 'watch']);