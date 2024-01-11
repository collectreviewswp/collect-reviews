<?php

namespace CollectReviews\Integrations;

use CollectReviews\Integrations\WooCommerce\Integration as WooCommerceIntegration;
use CollectReviews\Integrations\WPForms\Integration as WPFormsIntegration;
use CollectReviews\Integrations\EasyDigitalDownloads\Integration as EasyDigitalDownloadsIntegration;

/**
 * Class Integrations.
 *
 * @since 1.0.0
 */
class Integrations {

	/**
	 * Available integrations.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	const INTEGRATIONS = [
		'woocommerce'            => WooCommerceIntegration::class,
		'easy_digital_downloads' => EasyDigitalDownloadsIntegration::class,
		'wpforms'                => WPFormsIntegration::class
	];

	/**
	 * Get all integrations.
	 *
	 * @since 1.0.0
	 *
	 * @return AbstractIntegration[]
	 */
	public function get_integrations() {

		return array_map(
			function ( $integration_slug ) {
				return $this->get_integration( $integration_slug );
			},
			array_keys( self::INTEGRATIONS )
		);
	}

	/**
	 * Get integration by slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Integration slug.
	 *
	 * @return AbstractIntegration|null
	 */
	public function get_integration( $slug ) {

		if ( ! isset( self::INTEGRATIONS[ $slug ] ) ) {
			return null;
		}

		return collect_reviews()->get( self::INTEGRATIONS[ $slug ] );
	}
}
