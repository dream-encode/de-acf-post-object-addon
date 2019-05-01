var baseConfig = require('./')

// Configuration options
var CONFIG = {
	BASES: {
		src: baseConfig.sourceDirectory+'/assets/src/',
		dist: baseConfig.distDirectory+'/assets/dist/'
	},
	PATHS: {
		css: ['css/*'],
		sass: [ 'sass/*.scss', 'sass/**/*.scss' ],
		sassPartials: [ 'sass/**/*.scss' ],
		sassdoc: 'doc',
		all: '*',
		clean: [
			baseConfig.distDirectory+'/assets/dist/css/**/*',  
		]
	},
	SASS_OUTPUT_STYLE: baseConfig.ENV == 'dev' ? 'nested' : 'compressed', // nested, expanded, compact, compressed
	AUTOPREFIX_BROWSERS: [
		'last 5 versions',
		'ie >= 8',
		'ios >= 7',
		'android >= 4.4',
		'bb >= 10'
	],
	SASSDOC_OPTIONS: {
    dest: baseConfig.distDirectory+'/assets/dist/doc',
    verbose: true,
    display: {
      access: ['public', 'private'],
      alias: true,
      watermark: true,
    },
    groups: {
    },
  },
	IS_DEV: baseConfig.ENV == "dev" ? true : false
};

module.exports = CONFIG;
