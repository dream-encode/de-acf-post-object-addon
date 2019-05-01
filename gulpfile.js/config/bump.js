var baseConfig = require('./')

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'assets/src/',
		dist: baseConfig.distDirectory+'assets/dist/'
	},
	PATHS: {
		package: 'package.json',
		bower: 'bower.json'
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;
