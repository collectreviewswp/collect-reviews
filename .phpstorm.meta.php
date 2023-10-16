<?php

/**
 * PhpStorm helper that intructs IDE how its autocomplete should work.
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html
 */

namespace PHPSTORM_META {

	override(
		\CollectReviews\Core::get( 0 ),
		map(
			[
				'options'      => \CollectReviews\Options::class,
				'ajax'         => \CollectReviews\Ajax::class,
				'request'      => \CollectReviews\Request::class,
				'templates'    => \CollectReviews\TemplateLoader::class,
				'config'       => \CollectReviews\Config::class,
				'admin'        => \CollectReviews\Admin\Admin::class,
				'integrations' => \CollectReviews\Integrations\Integrations::class,
				'platforms'    => \CollectReviews\Platforms\Platforms::class,
			]
		)
	);
}
