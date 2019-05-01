//Misc files to move from src to dist.
//Various vendor libraries, and anything needed that isn't js/images/css/fonts/etc.
var config = require('./')

module.exports = {
  src: [CONFIG.BASES.src+'vendor/*'],
  base: CONFIG.BASES.src,
  dest: CONFIG.BASES.dist,
	IS_DEV: baseConfig.ENV == "dev"
}
