<?php

namespace CollectReviews\Integrations;

use WP_Error;

/**
 * Interface FormsIntegrationInterface.
 *
 * Interface for integrations with forms plugins.
 *
 * @since 1.0.0
 */
interface FormsIntegrationInterface {

	/**
	 * Get forms.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_forms();

	/**
	 * Get form fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $form_id
	 * @param string $field_type
	 *
	 * @return array|WP_Error
	 */
	public function get_form_fields( $form_id, $field_type = 'all' );
}
