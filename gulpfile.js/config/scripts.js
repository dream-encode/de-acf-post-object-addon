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
			baseConfig.distDirectory+'/assets/dist/js/**/*',  
		]
	},
	SCRIPT_UGLIFY_OPTIONS: {
		output: { 
			beautify: false,
			comments: /^!|\b(copyright|license)\b|@(preserve|license|cc_on)\b/i,
		},
		compress: {
			sequences     : true,
			properties    : true,
			dead_code     : true,
			drop_debugger : true, 
			unsafe        : false,
			conditionals  : true,
			comparisons   : true,
			evaluate      : true,
			booleans      : true,
			loops         : true,
			unused        : true,
			hoist_funs    : true,
			hoist_vars    : true,
			if_return     : true,
			join_vars     : true,
			cascade       : true, 
			side_effects  : true,
			warnings      : true,
		}
	},
	IS_DEV: baseConfig.ENV == "dev"
};

module.exports = CONFIG;