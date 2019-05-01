var baseConfig = require('./');

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'/assets/src/',
		dist: baseConfig.distDirectory+'/assets/dist/',
	},
	PATHS: {
		vendor: [
			baseConfig.sourceDirectory+'/assets/src/vendor/**/*.+(css|js|map|jpg|jpeg|png|gif|svg)', 
			"!"+baseConfig.sourceDirectory+'/assets/src/vendor/**/docs/**/*',
			"!"+baseConfig.sourceDirectory+'/assets/src/vendor/**/tests/**/*',
			"!"+baseConfig.sourceDirectory+'/assets/src/vendor/**/vendor/**/*',
			"!"+baseConfig.sourceDirectory+'/assets/src/vendor/**/Gruntfile.js',
			"!"+baseConfig.sourceDirectory+'/assets/src/vendor/select2/src/**/*',
		],
		clean: [
			baseConfig.distDirectory+'/assets/dist/vendor/**/*',  
		],
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;