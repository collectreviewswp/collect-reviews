<?php

namespace CollectReviews\Integrations\WPForms;

use CollectReviews\Helpers\Collection;
use CollectReviews\Integrations\AbstractHandler;
use CollectReviews\ReviewRequests\ReviewRequestsLimiter;

/**
 * Class Handler. Handles integration with WPForms.
 *
 * @since 1.0.0
 */
class Handler extends AbstractHandler {

	/**
	 * Handle form submission. Create review request if form submission matches any trigger.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields    Form fields.
	 * @param array $entry     Form entry.
	 * @param array $form_data Form data.
	 * @param int   $entry_id  Form entry ID.
	 */
	public function form_submitted( $fields, $entry, $form_data, $entry_id ) {

		if ( ! $this->integration->is_enabled() || ! $this->integration->is_configured() ) {
			return;
		}

		$form_id = isset( $entry['id'] ) ? intval( $entry['id'] ) : false;

		if ( empty( $form_id ) ) {
			return;
		}

		$options = $this->integration->get_options();

		// Skip early if any trigger doesn't have current form.
		$trigger_form_ids = array_column( array_column( $options['triggers'], 'form' ), 'id' );

		if ( ! in_array( $form_id, $trigger_form_ids, true ) ) {
			return;
		}

		$requests_frequency = collect_reviews()->get( 'options' )->get( 'review_request.frequency' );

		foreach ( $options['triggers'] as $trigger ) {
			$trigger = new Trigger( $trigger );

			if (
				! $trigger->is_enabled() ||
				! $trigger->is_valid() ||
				$trigger->get_form_id() !== $form_id
			) {
				continue;
			}

			$email = Collection::find( $fields, 'id', $trigger->get_email_field_id(), 'value' );

			if ( empty( $email ) || ! is_email( $email ) ) {
				continue;
			}

			$requests_limiter = new ReviewRequestsLimiter( $email );

			if ( $requests_limiter->exceeded( $requests_frequency ) ) {
				continue;
			}

			$review_request = $this->create_review_request( $trigger, $email );

			$review_request->set_meta( 'form_id', $form_id );
			$review_request->set_meta( 'entry_id', $entry_id );

			$name = Collection::find( $fields, 'id', $trigger->get_name_field_id() );

			if ( ! empty( $name ) && isset( $name['first'], $name['last'] ) ) {
				$review_request->set_meta( 'first_name', $name['first'] );
				$review_request->set_meta( 'last_name', $name['last'] );
			}

			$review_request->save();

			if ( $trigger->get_review_request_delay() === 0 ) {
				$review_request->send();
			}

			$requests_limiter->track();

			break;
		}
	}
}
