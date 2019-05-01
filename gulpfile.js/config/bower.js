var baseConfig = require('./')

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'assets/src/',
		dist: baseConfig.distDirectory+'assets/dist/'
	},
	PATHS: {
		all: '*'
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;
