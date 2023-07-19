<?php


namespace CollectReviews\Emails\SmartTags;

/**
 * Interface SmartTagInterface.
 *
 * @since 1.0.0
 */
interface SmartTagInterface {

	/**
	 * Get the smart tag value.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_value();
}
