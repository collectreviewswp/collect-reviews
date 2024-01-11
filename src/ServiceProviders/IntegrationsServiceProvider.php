<?php

namespace CollectReviews\ServiceProviders;

use CollectReviews\Integrations\Integrations;

/**
 * Main service provider.
 *
 * @since 1.0.0
 */
class IntegrationsServiceProvider extends AbstractServiceProvider {

	/**
	 * Register plugin services.
	 *
	 * @since 1.0.0
	 */
	public function register() {

		$this->container->add( Integrations::class )->setAlias( 'integrations' );

		foreach ( Integrations::INTEGRATIONS as $integration_class ) {
			$this->container->add( $integration_class );
		}
	}

	/**
	 * Get bootable services.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_bootable_services() {

		return array_values( Integrations::INTEGRATIONS );
	}
}
