<?php

namespace CollectReviews\Helpers;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Class Date. Helper class for date related functions.
 *
 * @since 1.0.0
 */
class Date {

	/**
	 * Date format used in the database.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const DB_DATE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Format date to the database format.
	 *
	 * @since 1.0.0
	 *
	 * @param DateTime $date Date instance.
	 *
	 * @return string
	 */
	public static function format_db_date( $date ) {

		if ( ! $date instanceof DateTime ) {
			return '';
		}

		return $date->format( self::DB_DATE_FORMAT );
	}

	/**
	 * Format date to the display format in site timezone.
	 *
	 * @since 1.0.0
	 *
	 * @param DateTime $date   Date instance.
	 * @param string   $format Date format.
	 *
	 * @return string
	 */
	public static function format_display_date( $date, $format = false ) {

		if ( ! $date instanceof DateTime ) {
			return '';
		}

		if ( ! $format ) {
			$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		}

		return $date->setTimezone( wp_timezone() )->format( $format );
	}

	/**
	 * Create date instance from string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $date Date string.
	 *
	 * @return DateTime|null
	 */
	public static function create( $date ) {

		if ( empty( $date ) ) {
			return null;
		}

		try {
			$date = new DateTime( $date, new DateTimeZone( 'UTC' ) );
		} catch ( Exception $e ) {
			$date = null;
		}

		return $date;
	}
}
