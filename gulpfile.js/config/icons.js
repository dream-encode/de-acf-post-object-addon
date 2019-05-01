var config = require('./')

module.exports = {
	font: {
		dest: CONFIG.BASES.src + 'fonts'
	},
	styles: {
		dest: CONFIG.BASES.src + 'sass/base'
	},
	images: {
		src: CONFIG.BASES.src + 'images/icons/*.svg'
	},
	IS_DEV: baseConfig.ENV == "dev"
}
