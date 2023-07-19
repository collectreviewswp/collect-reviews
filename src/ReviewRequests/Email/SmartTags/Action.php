<?php

namespace CollectReviews\ReviewRequests\Email\SmartTags;

use CollectReviews\Emails\SmartTags\SmartTagInterface;
use CollectReviews\Integrations\EcommerceIntegrationInterface;
use CollectReviews\Integrations\FormsIntegrationInterface;
use CollectReviews\ReviewRequests\ReviewRequest;

/**
 * Class Action. Represents the user action (e.g. made a purchase, submitted form, etc.).
 *
 * @since 1.0.0
 */
class Action implements SmartTagInterface {

	/**
	 * Review request.
	 *
	 * @since 1.0.0
	 *
	 * @var ReviewRequest
	 */
	private $review_request;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review request.
	 */
	public function __construct( $review_request ) {

		$this->review_request = $review_request;
	}

	/**
	 * Get the smart tag value.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_value() {

		$action           = '';
		$integration_slug = $this->review_request->get_integration();
		$integration      = collect_reviews()->get( 'integrations' )->get_integration( $integration_slug );

		if ( $integration instanceof EcommerceIntegrationInterface ) {
			$action = esc_html__( 'made a purchase', 'collect-reviews' );
		} else if ( $integration instanceof FormsIntegrationInterface ) {
			$action = esc_html__( 'submitted form', 'collect-reviews' );
		}

		return $action;
	}
}
