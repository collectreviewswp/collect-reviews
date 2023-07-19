<?php

namespace CollectReviews\Integrations;

/**
 * Interface EcommerceIntegrationInterface.
 *
 * Interface for integrations with E-commerce plugins.
 *
 * @since 1.0.0
 */
interface EcommerceIntegrationInterface {

	/**
	 * Get order statuses.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_order_statuses();
}
