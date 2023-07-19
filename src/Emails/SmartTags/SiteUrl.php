<?php

namespace CollectReviews\Emails\SmartTags;

/**
 * Class SiteUrl. Get the site url.
 *
 * @since 1.0.0
 */
class SiteUrl implements SmartTagInterface {

	/**
	 * Get the smart tag value.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_value() {

		return wp_parse_url( home_url(), PHP_URL_HOST );
	}
}
