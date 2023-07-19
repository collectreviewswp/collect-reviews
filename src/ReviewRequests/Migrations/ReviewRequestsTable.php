<?php

namespace CollectReviews\ReviewRequests\Migrations;

use CollectReviews\AbstractMigration;

/**
 * Class ReviewRequestsTable.
 *
 * Table for storing the review requests.
 *
 * @since 1.0.0
 */
class ReviewRequestsTable extends AbstractMigration {

	/**
	 * Get the table name.
	 *
	 * @since 1.0.0
	 */
	public static function get_raw_table_name() {

		return 'collect_reviews_review_requests';
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

		global $wpdb;

		$table   = self::get_table_name();
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		/*
		 * Create the table.
		 */
		$sql = "
		CREATE TABLE `$table` (
		    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		    `unique_key` VARCHAR(20) NOT NULL,
		    `status` TINYINT UNSIGNED NOT NULL DEFAULT '0',
		    `email` VARCHAR(320) NOT NULL,
		    `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		    `send_date` TIMESTAMP NOT NULL,
		    `integration` VARCHAR(255) NOT NULL,
		    `platform_type` VARCHAR(255) DEFAULT '',
		    `platform_name` VARCHAR(255) DEFAULT '',
		    `positive_review_url` TEXT DEFAULT  '',
		    PRIMARY KEY (id),
		    INDEX unique_key (unique_key),
		    INDEX status (status),
		    INDEX integration (integration),
		    INDEX platform_name (platform_name)
		) {$collate};";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->query( $sql );

		if ( ! empty( $wpdb->last_error ) ) {
			$this->set_error( $wpdb->last_error );
		}

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 1 );
		}
	}
}
