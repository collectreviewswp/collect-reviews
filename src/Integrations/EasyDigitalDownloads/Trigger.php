<?php

namespace CollectReviews\Integrations\EasyDigitalDownloads;

use CollectReviews\Integrations\AbstractTrigger;

/**
 * Class Trigger. Represents the trigger for the Easy Digital Downloads integration.
 *
 * @since 1.0.0
 */
class Trigger extends AbstractTrigger {

	/**
	 * Get trigger order status. The order status that triggers the review request.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_order_status() {

		return $this->options->get( 'order_status' );
	}

	/**
	 * Whether the trigger configuration is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {

		return parent::is_valid() && ! empty( $this->get_order_status() );
	}
}
