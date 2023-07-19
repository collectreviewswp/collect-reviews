<?php

namespace CollectReviews\ReviewRequests;

use CollectReviews\ReviewRequests\Migrations\ReviewRequestsLimitLogsTable;

/**
 * Class ReviewRequestsLimiter.
 *
 * This class is responsible for limiting the number of review requests
 * sent to a single customer (email address).
 *
 * @since 1.0.0
 */
class ReviewRequestsLimiter {

	/**
	 * Email address.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $email;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email Email address.
	 */
	public function __construct( $email ) {

		$this->email = $email;
	}

	/**
	 * Check if the number of review requests sent to a single customer has exceeded the limit.
	 *
	 * @since 1.0.0
	 *
	 * @param int $frequency Frequency of sending review requests. In days. 0 - no limit.
	 *
	 * @return bool
	 */
	public function exceeded( $frequency ) {

		if ( $frequency === 0 ) {
			return false;
		}

		global $wpdb;

		$table = ReviewRequestsLimitLogsTable::get_table_name();

		if ( $frequency > 0 ) {
			$query = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE email = %s AND last_created_date > DATE_SUB(%s, INTERVAL %d DAY)",
				$this->email,
				current_time( 'mysql', true ),
				$frequency
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE email = %s",
				$this->email
			);
		}

		$result = $wpdb->get_row( $query );

		return ! empty( $result );
	}

	/**
	 * Record that a review request has been created for particular customer.
	 *
	 * @since 1.0.0
	 */
	public function track() {

		global $wpdb;

		$table = ReviewRequestsLimitLogsTable::get_table_name();

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$table} (email, last_created_date) VALUES (%s, %s)
				ON DUPLICATE KEY UPDATE last_created_date=values(last_created_date)",
				$this->email,
				current_time( 'mysql', true )
			)
		);
	}
}

