<?php

namespace CollectReviews\ReviewRequests\Migrations;

use CollectReviews\DatabaseMigrations\AbstractTableMigration;

/**
 * Class ReviewRequestsMetaTable.
 *
 * Table for storing the metadata of the review requests.
 *
 * @since 1.0.0
 */
class ReviewRequestsMetaTable extends AbstractTableMigration {

	/**
	 * Get the table name.
	 *
	 * @since 1.0.0
	 */
	public static function get_raw_table_name() {

		return 'collect_reviews_review_requests_meta';
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
				meta_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				review_request_id BIGINT UNSIGNED NOT NULL,
				meta_key VARCHAR(255) DEFAULT NULL,
				meta_value LONGTEXT DEFAULT NULL,
				PRIMARY KEY (meta_id),
				UNIQUE KEY review_request_id_meta_key (review_request_id,meta_key),
				INDEX review_request_id (review_request_id),
				INDEX meta_key (meta_key)
		) {$collate};";

		dbDelta( $sql );

		if ( empty( $wpdb->last_error ) ) {
			$this->update_db_ver( 1 );
		} else {
			$this->set_error( $wpdb->last_error );
		}
	}
}
