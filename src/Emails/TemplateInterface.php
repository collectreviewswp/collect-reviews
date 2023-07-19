<?php

namespace CollectReviews\Emails;

/**
 * Interface TemplateInterface.
 *
 * @since 1.0.0
 */
interface TemplateInterface {

	/**
	 * Get the template HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get();
}
