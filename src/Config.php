<?php

namespace CollectReviews;

use CollectReviews\Helpers\CollectionAccess;

/**
 * Class Config.
 *
 * Store all the config values.
 *
 * @since 1.0.0
 */
class Config {

	/**
	 * Config values.
	 *
	 * @since 1.0.0
	 *
	 * @var CollectionAccess
	 */
	private $config;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->config = new CollectionAccess(
			[
				'platforms' => [
					[
						'slug' => 'google',
						'name' => esc_html__( 'Google', 'collect-reviews' ),
					],
					[
						'slug' => 'trustpilot',
						'name' => esc_html__( 'Trustpilot', 'collect-reviews' ),
					],
					[
						'slug' => 'facebook',
						'name' => esc_html__( 'Facebook', 'collect-reviews' ),
					],
					[
						'slug' => 'tripadvisor',
						'name' => esc_html__( 'Tripadvisor', 'collect-reviews' ),
					],
					[
						'slug' => 'yelp',
						'name' => esc_html__( 'Yelp', 'collect-reviews' ),
					]
				]
			]
		);
	}

	/**
	 * Get a value from config.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Key.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {

		return $this->config->get( $key, $default );
	}

	/**
	 * Get all config values.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_all() {

		return $this->config->get_all();
	}
}
