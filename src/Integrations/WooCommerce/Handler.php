<?php

namespace CollectReviews\Integrations\WooCommerce;

use CollectReviews\Integrations\AbstractHandler;
use CollectReviews\ReviewRequests\ReviewRequestsLimiter;
use WC_Order;

/**
 * Class Handler. Handles integration with WooCommerce.
 *
 * @since 1.0.0
 */
class Handler extends AbstractHandler {

	/**
	 * Handle order status change. Create review request if order status matches any trigger.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $order_id    Order ID.
	 * @param string|null $status_from Old order status.
	 * @param string|null $status_to   New order status.
	 * @param WC_Order    $order       Order instance.
	 */
	public function order_status_changed( $order_id, $status_from, $status_to, WC_Order $order ) {

		if ( ! $this->integration->is_enabled() || ! $this->integration->is_configured() ) {
			return;
		}

		if ( empty( $order ) ) {
			$order = wc_get_order( $order_id );
		}

		if (
			empty( $order ) ||
			$order->get_type() !== 'shop_order' ||
			! empty( $order->get_meta( 'collect_reviews_review_request_sent' ) )
		) {
			return;
		}

		$options = $this->integration->get_options();

		// Skip early if any trigger doesn't have current order status.
		$trigger_order_status = array_column( $options['triggers'], 'order_status' );

		if ( ! $order->has_status( $trigger_order_status ) ) {
			return;
		}

		$email              = $order->get_billing_email();
		$requests_frequency = collect_reviews()->get( 'options' )->get( 'review_request.frequency' );

		$requests_limiter = new ReviewRequestsLimiter( $email );

		if ( $requests_limiter->exceeded( $requests_frequency ) ) {
			return;
		}

		foreach ( $options['triggers'] as $trigger ) {
			$trigger = new Trigger( $trigger );

			if (
				! $trigger->is_enabled() ||
				! $trigger->is_valid() ||
				! $order->has_status( $trigger->get_order_status() )
			) {
				continue;
			}

			$review_request = $this->create_review_request( $trigger, $email );

			$review_request->set_meta( 'order_id', $order->get_id() );
			$review_request->set_meta( 'first_name', $order->get_billing_first_name() );
			$review_request->set_meta( 'last_name', $order->get_billing_last_name() );

			$review_request->save();

			if ( $trigger->get_review_request_delay() === 0 ) {
				$review_request->send();
			}

			$requests_limiter->track();

			$order->update_meta_data( 'collect_reviews_review_request_sent', true );
			$order->save_meta_data();

			break;
		}
	}
}
