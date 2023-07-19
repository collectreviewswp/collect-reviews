<?php

namespace CollectReviews\ReviewRequests;

use CollectReviews\Helpers\Date;
use CollectReviews\ModuleInterface;

/**
 * Class Queue.
 *
 * Review requests queue.
 *
 * @since 1.0.0
 */
class Queue implements ModuleInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( ! wp_next_scheduled( 'collect_reviews_review_requests_queue' ) ) {
			wp_schedule_event( $this->get_job_start_time(), 'hourly', 'collect_reviews_review_requests_queue' );
		}
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'collect_reviews_review_requests_queue', [ $this, 'process' ] );
	}

	/**
	 * Process queue. Send review requests.
	 *
	 * Send max 20 review requests per hour.
	 *
	 * @since 1.0.0
	 */
	public function process() {

		$args = [
			'status'     => ReviewRequest::STATUS_PENDING,
			'per_page'   => 20,
			'date_query' => [
				[
					'column'    => 'send_date',
					'before'    => current_time( 'mysql', true ),
					'inclusive' => true,
				],
			],
		];

		$review_requests_query = new ReviewRequestsQuery( $args );
		$review_requests       = $review_requests_query->query();

		foreach ( $review_requests as $review_request ) {
			$review_request->send();
		}
	}

	/**
	 * Get job start time.
	 *
	 * Start job at the beginning of the next hour. We need this since while manual
	 * review request creation it's allowed to set only hours in the "Sent date".
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	private function get_job_start_time() {

		$date    = Date::create( 'now' );
		$minutes = $date->format( 'i' );

		if ( $minutes > 0 ) {
			$date->modify( '+1 hour' );
			$date->modify( '-' . $minutes . ' minutes' );
		}

		return $date->getTimestamp();
	}
}
