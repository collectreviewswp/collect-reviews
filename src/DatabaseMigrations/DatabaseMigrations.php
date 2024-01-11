<?php

namespace CollectReviews\DatabaseMigrations;

use CollectReviews\ReviewRequests\Migrations\ReviewRequestsLimitLogsTable;
use CollectReviews\ReviewRequests\Migrations\ReviewRequestsMetaTable;
use CollectReviews\ReviewRequests\Migrations\ReviewRequestsTable;

/**
 * Class DatabaseMigrations.
 *
 * @since 1.0.0
 */
class DatabaseMigrations {

	/**
	 * List of migrations classes.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	const MIGRATIONS = [
		ReviewRequestsTable::class,
		ReviewRequestsMetaTable::class,
		ReviewRequestsLimitLogsTable::class,
	];

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->define_tables();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		if ( collect_reviews()->get( 'request' )->is_admin() ) {
			add_action( 'admin_init', [ $this, 'validate_db' ] );
		}
	}

	/**
	 * Check DB version and update to the latest one.
	 *
	 * @since 1.0.0
	 */
	public function validate_db() {

		foreach ( self::MIGRATIONS as $class_name ) {
			$migration = new $class_name();
			$migration->validate_db();
		}
	}

	/**
	 * Register custom tables within `$wpdb` object.
	 *
	 * @since 1.0.0
	 */
	private function define_tables() {
		global $wpdb;

		foreach ( self::MIGRATIONS as $migration ) {
			$table_name        = $migration::get_raw_table_name();
			$wpdb->$table_name = $wpdb->prefix . $table_name;
			$wpdb->tables[]    = $table_name;
		}
	}
}
