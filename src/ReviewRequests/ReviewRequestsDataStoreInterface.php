<?php

namespace CollectReviews\ReviewRequests;

use CollectReviews\DataStore\ObjectDataStoreInterface;
use CollectReviews\DataStore\ObjectMetaDataStoreInterface;

/**
 * Interface ReviewRequestsDataStoreInterface.
 *
 * All CRUD operations interface for Review Requests.
 *
 * @since 1.0.0
 */
interface ReviewRequestsDataStoreInterface extends ObjectDataStoreInterface, ObjectMetaDataStoreInterface {

	/**
	 * Get review requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return ReviewRequest[]|array
	 */
	public function query( $args );

	/**
	 * Get total number of Review Requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return int
	 */
	public function get_count( $args );
}
