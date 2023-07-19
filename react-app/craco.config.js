module.exports = {
	webpack: {
		configure: {
			externals: {
				'@wordpress/i18n': [ 'window wp', 'i18n' ],
			},
		},
	},
	devServer: {
		devMiddleware: {
			writeToDisk: true,
		},
	}
};
