<?php

namespace CollectReviews\Integrations\WooCommerce;

use CollectReviews\Integrations\AbstractIntegration;
use CollectReviews\Integrations\EcommerceIntegrationInterface;

/**
 * Class Integration. Represents the WooCommerce integration.
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

		add_action( 'woocommerce_order_status_changed', [ $this->handler, 'order_status_changed' ], 10, 4 );
	}

	/**
	 * Get the slug of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_slug() {

		return 'woocommerce';
	}

	/**
	 * Get the title of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {

		return __( 'WooCommerce', 'collect-reviews' );
	}

	/**
	 * Whether the integration plugin is installed and activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_available() {

		return function_exists( 'WC' );
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
	 * Get WooCommerce order statuses.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_order_statuses() {

		if ( ! $this->is_available() ) {
			return [];
		}

		$order_statuses = wc_get_order_statuses();

		// Remove wc- prefix from order statuses.
		foreach ( $order_statuses as $key => $value ) {
			$order_statuses[ str_replace( 'wc-', '', $key ) ] = $value;
			unset( $order_statuses[ $key ] );
		}

		return $order_statuses;
	}
}
