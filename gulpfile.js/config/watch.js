var baseConfig = require('./');

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'/assets/src/',
		dist: baseConfig.distDirectory+'/assets/dist/',
	},
	PATHS: {
		fonts: [
			baseConfig.sourceDirectory+'/assets/src/fonts/*',
			baseConfig.sourceDirectory+'/assets/src/fonts/**/*',
		],
		images: [ 
			baseConfig.sourceDirectory+'/assets/src/images/*.{jpg,jpeg,png,gif}', 
			baseConfig.sourceDirectory+'/assets/src/images/**/*.{jpg,jpeg,png,gif}', 
		],
		vendor: [ 
			baseConfig.sourceDirectory+'/assets/src/vendor/*', 
			baseConfig.sourceDirectory+'/assets/src/vendor/**/*', 
		],
		sass: [ 
			baseConfig.sourceDirectory+'/assets/src/sass/**/*.scss',
			baseConfig.sourceDirectory+'/assets/src/sass/*.scss',
		],
		scripts: [ 
			baseConfig.sourceDirectory+'/assets/src/js/*.js',
			baseConfig.sourceDirectory+'/assets/src/js/**/*.js',
		],
		all: [
			baseConfig.sourceDirectory+'/assets/src/**/*',
			baseConfig.sourceDirectory+'/assets/src/*',
		],
		clean: [
			baseConfig.distDirectory+'/assets/dist/*',  
		]
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;