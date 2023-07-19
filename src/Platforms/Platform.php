<?php

namespace CollectReviews\Platforms;

use CollectReviews\Helpers\Collection;

/**
 * Class Platform.
 *
 * Represents a platform for collect reviews.
 *
 * @since 1.0.0
 */
class Platform {

	/**
	 * The platform type (e.g. google, facebook, or custom).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The platform name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The platform review url.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $review_url;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data The platform data.
	 */
	public function __construct( $data ) {

		$data = wp_parse_args( $data, [
			'type'       => '',
			'name'       => '',
			'review_url' => '',
		] );

		$this->set_type( $data['type'] );
		$this->set_name( $data['name'] );
		$this->set_review_url( $data['review_url'] );
	}

	/**
	 * Set the platform type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The platform type.
	 */
	public function set_type( $type ) {

		$this->type = $type;
	}

	/**
	 * Get the platform type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type() {

		return $this->type;
	}

	/**
	 * Set the platform name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The platform name.
	 */
	public function set_name( $name ) {

		$this->name = $name;
	}

	/**
	 * Get the platform name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {

		// If name is not defined, try to get it from the platforms config based on the platform type.
		if ( empty( $this->name ) && $this->type !== 'custom' ) {
			$platforms  = collect_reviews()->get( 'config' )->get( 'platforms' );
			$this->name = Collection::find( $platforms, 'slug', $this->type, 'name', ucfirst( $this->type ) );
		}

		return $this->name;
	}

	/**
	 * Set the platform review url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $review_url The platform review url.
	 */
	public function set_review_url( $review_url ) {

		$this->review_url = $review_url;
	}

	/**
	 * Get the platform review url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_review_url() {

		return $this->review_url;
	}

	/**
	 * Whether the platform is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {

		if ( ! filter_var( $this->get_review_url(), FILTER_VALIDATE_URL ) ) {
			return false;
		}

		return true;
	}
}
