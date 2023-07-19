<?php

namespace CollectReviews\Emails;

/**
 * Interface EmailInterface.
 *
 * @since 1.0.0
 */
interface EmailInterface {

	/**
	 * Send email.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function send();
}
