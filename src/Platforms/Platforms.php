<?php

namespace CollectReviews\Platforms;

/**
 * Class Platforms.
 *
 * @since 1.0.0
 */
class Platforms {

	/**
	 * Get all platforms data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_platforms_data() {

		return [
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
		];
	}
}
