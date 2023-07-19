<?php

namespace CollectReviews;

/**
 * Class AbstractMigration.
 *
 * Helps migrate plugin DB tables.
 *
 * current version - represent current version in the DB.
 * latest version - represent latest version in the code.
 *
 * Most of the time these versions should match. But, if current version is lower than latest version,
 * then migration will be triggered.
 *
 * @since 1.0.0
 */
abstract class AbstractMigration {

	/**
	 * Current migration version.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $current_version;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->current_version = static::get_current_version();
	}

	/**
	 * Initialize migration.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->validate_db();
	}

	/**
	 * Get table name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	abstract public static function get_raw_table_name();

	/**
	 * Get table name with DB prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_table_name() {

		global $wpdb;

		return $wpdb->prefix . static::get_raw_table_name();
	}

	/**
	 * Version of the latest migration.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	abstract public static function get_latest_version();

	/**
	 * Get current migration version option name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected static function get_current_version_option_name() {

		return static::get_raw_table_name() . '_migration_version';
	}

	/**
	 * Get migration error option name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected static function get_migration_error_option_name() {

		return static::get_raw_table_name() . '_migration_error';
	}

	/**
	 * Whether migration is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_enabled() {

		return true;
	}

	/**
	 * Get current migration version.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public static function get_current_version() {

		return (int) get_option( self::get_current_version_option_name(), 0 );
	}

	/**
	 * Check DB version and update to the latest one.
	 *
	 * @since 1.0.0
	 */
	protected function validate_db() {

		if ( $this->current_version < static::get_latest_version() ) {
			$this->run( static::get_latest_version() );
		}
	}

	/**
	 * Update DB version in options table.
	 *
	 * @since 1.0.0
	 *
	 * @param int $version Version number.
	 */
	protected function update_db_ver( $version ) {

		$version = (int) $version;

		update_option( self::get_current_version_option_name(), $version, true );

		// Clear error option after DB version update.
		$this->set_error( '' );
	}

	/**
	 * Run migration.
	 *
	 * @since 1.0.0
	 *
	 * @param int $version Migration version to run.
	 */
	protected function run( $version ) {

		$version = (int) $version;

		if ( method_exists( $this, 'migrate_to_' . $version ) ) {
			$this->{'migrate_to_' . $version}();
		}
	}

	/**
	 * Set migration error.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Error message.
	 */
	protected function set_error( $message ) {

		if ( ! empty( $message ) ) {
			update_option( self::get_migration_error_option_name(), $message, false );
		} else {
			delete_option( self::get_migration_error_option_name() );
		}
	}
}
