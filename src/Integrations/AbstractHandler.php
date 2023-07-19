<?php

namespace CollectReviews\Integrations;

use CollectReviews\Helpers\Date;
use CollectReviews\ReviewRequests\ReviewRequest;
use DateInterval;

/**
 * Class AbstractHandler. Base class for integration handlers.
 *
 * Handles the creation of review requests.
 *
 * @since 1.0.0
 */
class AbstractHandler {

	/**
	 * The integration instance.
	 *
	 * @since 1.0.0
	 *
	 * @var AbstractIntegration
	 */
	protected $integration;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param AbstractIntegration $integration The integration instance.
	 */
	public function __construct( AbstractIntegration $integration ) {

		$this->integration = $integration;
	}

	/**
	 * Creates a review request.
	 *
	 * @since 1.0.0
	 *
	 * @param AbstractTrigger $trigger The trigger instance.
	 * @param string          $email   The email address.
	 *
	 * @return ReviewRequest
	 */
	protected function create_review_request( AbstractTrigger $trigger, $email ) {

		$review_request = new ReviewRequest();

		$request_delay       = $trigger->get_review_request_delay();
		$created_date        = Date::create( 'now' );
		$request_review_date = clone $created_date;

		if ( $request_delay > 0 ) {
			$request_review_date->add( new DateInterval( "PT{$request_delay}S" ) );
			$request_review_date->setTime( $request_review_date->format( 'H' ), 0 );
		}

		$review_request->set_status( ReviewRequest::STATUS_PENDING );
		$review_request->set_email( $email );
		$review_request->set_created_date( $created_date );
		$review_request->set_send_date( $request_review_date );

		// Set first valid platform as default.
		foreach ( $trigger->get_platforms() as $platform ) {
			if ( ! $platform->is_valid() ) {
				continue;
			}

			$review_request->set_platform_type( $platform->get_type() );
			$review_request->set_platform_name( $platform->get_name() );
			$review_request->set_positive_review_url( $platform->get_review_url() );
			break;
		}

		$review_request->set_integration( $this->integration->get_slug() );

		return $review_request;
	}
}
