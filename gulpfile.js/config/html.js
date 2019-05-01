var config = require('./')

module.exports = {
  watch: CONFIG.BASES.src + 'templates/**/*.{html,php}',
  src: [CONFIG.BASES.src + 'templates/**/*.{html,php}'],
  dest: CONFIG.BASES.dist + 'templates/',
	IS_DEV: baseConfig.ENV == "dev"
}
