var baseConfig = require('./')

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'/assets/src/',
		dist: baseConfig.distDirectory+'/assets/dist/'
	},
	PATHS: {
		images: ['images/**/*.{jpg,jpeg,png,gif}'],
		all: '*',
		clean: [
			baseConfig.distDirectory+'/assets/dist/images/**/*',  
		]
	},
	IMGMIN_OPTIONS: {
		optimizationLevel: 3,
		progessive: true,
		interlaced: true
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;
