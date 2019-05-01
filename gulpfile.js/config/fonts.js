var baseConfig = require('./')

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'assets/src/',
		dist: baseConfig.distDirectory+'assets/dist/'
	},
	PATHS: {
		fonts: 'fonts/*',
		clean: [
			baseConfig.distDirectory+'/assets/dist/fonts/**/*',  
		]
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;
