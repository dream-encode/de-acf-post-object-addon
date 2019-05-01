var baseConfig = require('./');

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'/assets/src/',
		dist: baseConfig.distDirectory+'/assets/dist/',
	},
	PATHS: {
		scripts: ['js/*.js'],
		all: '*',
		clean: [
			baseConfig.distDirectory+'/assets/dist/js/modernizr.js',  
		]
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;