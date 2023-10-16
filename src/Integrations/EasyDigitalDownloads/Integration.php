<?php

namespace CollectReviews\Integrations\EasyDigitalDownloads;

use CollectReviews\Integrations\AbstractIntegration;
use CollectReviews\Integrations\EcommerceIntegrationInterface;

/**
 * Class Integration. Represents the Easy Digital Downloads integration.
 *
 * @since 1.0.0
 */
class Integration extends AbstractIntegration implements EcommerceIntegrationInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->handler = new Handler( $this );
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'edd_before_payment_status_change', [ $this->handler, 'order_status_changed' ], 10, 3 );
	}

	/**
	 * Get the slug of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_slug() {

		return 'easy_digital_downloads';
	}

	/**
	 * Get the title of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {

		return __( 'Easy Digital Downloads', 'collect-reviews' );
	}

	/**
	 * Whether the integration plugin is installed and activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_available() {

		return function_exists( 'EDD' );
	}

	/**
	 * Get the configured triggers of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return Trigger[]
	 */
	public function get_triggers() {

		return array_map( function ( $trigger ) {
			return new Trigger( $trigger );
		}, $this->options['triggers'] ?? [] );
	}

	/**
	 * Get Easy Digital Downloads order statuses.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_order_statuses() {

		if ( ! $this->is_available() ) {
			return [];
		}

		return edd_get_payment_statuses();
	}
}
