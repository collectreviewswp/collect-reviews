module.exports = {
	webpack: {
		configure: (webpackConfig, { env, paths }) => {

			webpackConfig.externals = {
				'@wordpress/i18n': ['window wp', 'i18n'],
			};

			if( env === 'production' ) {
				webpackConfig.optimization = {
					splitChunks: {
						// Include node_modules in the separate chunk.
						chunks: 'all',
						// Add "min.js" extension to the node_modules chunks to prevent those files parsing while translation (POT) file generation on translate.wordpress.org.
						cacheGroups: {
							defaultVendors: {
								test: /[\\/]node_modules[\\/]/,
								priority: -10,
								reuseExistingChunk: true,
								filename: 'static/js/[name].[contenthash:8].chunk.min.js',
							},
						},
					},
				};
			}

			return webpackConfig;
		},
	},
	devServer: {
		devMiddleware: {
			writeToDisk: true,
		},
	}
};
