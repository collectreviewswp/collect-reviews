<?php

namespace CollectReviews\Integrations\EasyDigitalDownloads;

use CollectReviews\Integrations\AbstractHandler;
use CollectReviews\ReviewRequests\ReviewRequestsLimiter;

/**
 * Class Handler. Handles integration with Easy Digital Downloads.
 *
 * @since 1.0.0
 */
class Handler extends AbstractHandler {

	/**
	 * Handle order status change. Create review request if order status matches any trigger.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $payment_id Payment ID.
	 * @param string $new_status New order status.
	 * @param string $old_status Old order status.
	 */
	public function order_status_changed( $payment_id, $new_status, $old_status ) {

		if ( ! $this->integration->is_enabled() || ! $this->integration->is_configured() ) {
			return;
		}

		$payment = edd_get_payment( $payment_id );

		if ( empty( $payment ) || ! empty( $payment->get_meta( 'collect_reviews_review_request_sent' ) ) ) {
			return;
		}

		$options = $this->integration->get_options();

		// Skip early if any trigger doesn't have current order status.
		$trigger_order_statuses = array_column( $options['triggers'], 'order_status' );

		if ( ! in_array( $new_status, $trigger_order_statuses ) ) {
			return;
		}

		$email              = $payment->email;
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
				$new_status !== $trigger->get_order_status()
			) {
				continue;
			}

			$review_request = $this->create_review_request( $trigger, $email );

			$review_request->set_meta( 'payment_id', $payment->ID );
			$review_request->set_meta( 'first_name', $payment->first_name );
			$review_request->set_meta( 'last_name', $payment->last_name );

			$review_request->save();

			if ( $trigger->get_review_request_delay() === 0 ) {
				$review_request->send();
			}

			$requests_limiter->track();

			$payment->update_meta( 'collect_reviews_review_request_sent', true );

			break;
		}
	}
}
