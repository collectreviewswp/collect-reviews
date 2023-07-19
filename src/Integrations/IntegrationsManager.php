<?php

namespace CollectReviews\Integrations;

use CollectReviews\Integrations\WooCommerce\Integration as WooCommerceIntegration;
use CollectReviews\Integrations\WPForms\Integration as WPFormsIntegration;
use CollectReviews\Integrations\EasyDigitalDownloads\Integration as EasyDigitalDownloadsIntegration;

/**
 * Class IntegrationsManager. Integrations initialization.
 *
 * @since 1.0.0
 */
class IntegrationsManager {

	/**
	 * Integrations instances.
	 *
	 * @since 1.0.0
	 *
	 * @var AbstractIntegration[]
	 */
	private $integrations = null;

	/**
	 * Initialize integrations.
	 *
	 * @since 1.0.0
	 */
	public function init_integrations() {

		foreach ( $this->get_integrations() as $integration ) {
			$integration->init();
		}
	}

	/**
	 * Get all integrations.
	 *
	 * @since 1.0.0
	 *
	 * @return AbstractIntegration[]
	 */
	public function get_integrations() {

		if ( is_null( $this->integrations ) ) {
			$this->integrations = [
				WooCommerceIntegration::get_slug()          => new WooCommerceIntegration(),
				EasyDigitalDownloadsIntegration::get_slug() => new EasyDigitalDownloadsIntegration(),
				WPFormsIntegration::get_slug()              => new WPFormsIntegration(),
			];
		}

		return $this->integrations;
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

		$integrations = $this->get_integrations();

		return $integrations[ $slug ] ?? null;
	}
}
