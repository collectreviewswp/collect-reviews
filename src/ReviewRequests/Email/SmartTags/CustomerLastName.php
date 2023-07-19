<?php

namespace CollectReviews\ReviewRequests\Email\SmartTags;

use CollectReviews\Emails\SmartTags\SmartTagInterface;
use CollectReviews\ReviewRequests\ReviewRequest;

/**
 * Class CustomerLastName. Represents the customer last name.
 *
 * @since 1.0.0
 */
class CustomerLastName implements SmartTagInterface {

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

		return $this->review_request->get_meta( 'last_name', '' );
	}
}
