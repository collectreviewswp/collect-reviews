<?php

namespace CollectReviews\ReviewRequests\Migrations;

use CollectReviews\DatabaseMigrations\AbstractTableMigration;

/**
 * Class ReviewRequestsLimitLogsTable.
 *
 * Table for storing the logs of the review request.
 * Used to limit the number of review requests sent to a single customer (email).
 *
 * @since 1.0.0
 */
class ReviewRequestsLimitLogsTable extends AbstractTableMigration {

	/**
	 * Get the table name.
	 *
	 * @since 1.0.0
	 */
	public static function get_raw_table_name() {

		return 'collect_reviews_review_requests_limit_logs';
	}

	/**
	 * Get migration latest version.
	 *
	 * @since 1.0.0
	 */
	public static function get_latest_version() {

		return 1;
	}

	/**
	 * Create the table.
	 *
	 * @since 1.0.0
	 */
	protected function migrate_to_1() {

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;

		$table   = self::get_table_name();
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		/*
		 * Create the table.
		 */
		$sql = "CREATE TABLE $table (
				id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				email VARCHAR(320) NOT NULL,
				last_created_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY email (email)
		) {$collate};";

		dbDelta( $sql );

		if ( empty( $wpdb->last_error ) ) {
			$this->update_db_ver( 1 );
		} else {
			$this->set_error( $wpdb->last_error );
		}
	}
}
