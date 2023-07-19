<?php

namespace CollectReviews\Integrations\WPForms;

use CollectReviews\Integrations\AbstractIntegration;
use CollectReviews\Integrations\FormsIntegrationInterface;
use WP_Error;

/**
 * Class Integration. Represents the WPForms integration.
 *
 * @since 1.0.0
 */
class Integration extends AbstractIntegration implements FormsIntegrationInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->handler = new Handler( $this );
	}

	/**
	 * Get the slug of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_slug() {

		return 'wpforms';
	}

	/**
	 * Get the title of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {

		return __( 'WPForms', 'collect-reviews' );
	}

	/**
	 * Whether the integration plugin is installed and activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_available() {

		return function_exists( 'wpforms' );
	}

	/**
	 * Get the configured triggers of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return Trigger[]
	 */
	public function get_triggers() {

		return array_map( function ( $trigger ) {
			return new Trigger( $trigger );
		}, $this->options['triggers'] ?? [] );
	}

	/**
	 * Get all forms.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_forms() {

		if ( ! $this->is_available() ) {
			return [];
		}

		$forms = wpforms()->get( 'form' )->get(
			'',
			[
				'orderby' => 'title',
				'order'   => 'ASC',
			]
		);

		if ( empty( $forms ) ) {
			return [];
		}

		return array_map( function ( $post ) {
			return [
				'id'    => intval( $post->ID ),
				'title' => $post->post_title,
			];
		}, $forms );
	}

	/**
	 * Get form fields.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $form_id    Form ID.
	 * @param string $field_type Field type.
	 *
	 * @return array|WP_Error
	 */
	public function get_form_fields( $form_id, $field_type = 'all' ) {

		if ( ! $this->is_available() ) {
			return [];
		}

		$form = wpforms()->get( 'form' )->get( $form_id, [ 'content_only' => true ] );

		if ( empty( $form ) ) {
			return new WP_Error( 'invalid_form_id', __( 'Invalid form ID.', 'collect-reviews' ) );
		} else if ( empty( $form['fields'] ) ) {
			return [];
		}

		$fields = array_values( $form['fields'] );

		if ( $field_type !== 'all' ) {
			$fields = array_values( array_filter( $fields, function ( $field ) use ( $field_type ) {
				return isset( $field['type'] ) && $field['type'] === $field_type;
			} ) );
		}

		return array_map( function ( $field ) {
			return [
				'id'    => intval( $field['id'] ),
				'title' => $field['label'],
			];
		}, $fields );
	}
}
