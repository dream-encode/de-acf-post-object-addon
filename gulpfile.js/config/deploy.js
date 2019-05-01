var config = require('./')

module.exports = {
	src: ['./assets{,!/src/**}', './.htaccess', './favicon.ico', 'index.php'],
	dev: {
		root: 				'.',
		destination: 		'blank.dream-encode.com/public_html/',
		hostname: 			'192.168.1.26',
		username: 			'david',
		relative: 			true,
		emptyDirectories: 	true,
		recursive: 			true,
		incremental: 		true,
		exclude: ['**/assets/src{,!/**}'],
	},
	production: {
		root: 				'.',
		destination: 		'/home/blank/public_html',
		hostname: 			'66.23.239.90',
		username: 			'david',
		relative: 			true,
		emptyDirectories: 	true,
		recursive: 			true,
		incremental: 		false,
		exclude: ['**/assets/src{,!/**}'],
	},
	IS_DEV: baseConfig.ENV == "dev"
}
