<?php

namespace CollectReviews\Integrations;

use CollectReviews\Helpers\CollectionAccess;
use CollectReviews\Platforms\Platform;
use DateInterval;
use Exception;

/**
 * Class AbstractTrigger. Base class for integration triggers.
 *
 * Holds trigger options and provides trigger's methods.
 *
 * @since 1.0.0
 */
class AbstractTrigger {

	/**
	 * Trigger options.
	 *
	 * @since 1.0.0
	 *
	 * @var CollectionAccess
	 */
	protected $options;

	/**
	 * Trigger platforms.
	 *
	 * @since 1.0.0
	 *
	 * @var Platform[]
	 */
	private $platforms = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Trigger options.
	 */
	public function __construct( $options ) {

		$this->options = new CollectionAccess( $options );
	}

	/**
	 * Whether the trigger is enabled.
	 *
	 * @since 1.0.0
	 */
	public function is_enabled() {

		return true;
	}

	/**
	 * Get review request delay in seconds.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_review_request_delay() {

		return (int) $this->options->get( 'review_request_delay', DAY_IN_SECONDS * 14 );
	}

	/**
	 * Get trigger platforms.
	 *
	 * @since 1.0.0
	 *
	 * @return Platform[]
	 */
	public function get_platforms() {

		if ( is_null( $this->platforms ) ) {
			$this->platforms = array_map( function ( $platform ) {
				return new Platform( $platform );
			}, $this->options->get( 'platforms', [] ) );
		}

		return $this->platforms;
	}

	/**
	 * Whether the trigger configuration is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {

		// Whether at least one platform is valid.
		foreach ( $this->get_platforms() as $platform ) {
			if ( $platform->is_valid() ) {
				return true;
			}
		}

		return false;
	}
}
