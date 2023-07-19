<?php

namespace CollectReviews\Emails\SmartTags;

/**
 * Class SiteName. Get the site name.
 *
 * @since 1.0.0
 */
class SiteName implements SmartTagInterface {

	/**
	 * Get the smart tag value.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_value() {

		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
}
