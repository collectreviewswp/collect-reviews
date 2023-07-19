<?php

namespace CollectReviews\Integrations\WPForms;

use CollectReviews\Integrations\AbstractTrigger;

/**
 * Class Trigger. Represents the trigger for the WPForms integration.
 *
 * @since 1.0.0
 */
class Trigger extends AbstractTrigger {

	/**
	 * Get the form ID. The ID of the form that triggers the review request after submission.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_id() {

		return $this->options->get( 'form.id' );
	}

	/**
	 * Get the email field ID. Email from this field will be used to send the review request.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_email_field_id() {

		return $this->options->get( 'form_email_field.id' );
	}

	/**
	 * Get the name field ID. Name from this field will be used in the review request content.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_name_field_id() {

		return $this->options->get( 'form_name_field.id' );
	}

	/**
	 * Whether the trigger configuration is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {

		return parent::is_valid() && ! empty( $this->get_form_id() ) && ! empty( $this->get_email_field_id() );
	}
}
