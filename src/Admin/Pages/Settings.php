<?php

namespace CollectReviews\Admin\Pages;

use CollectReviews\Ajax\Ajaxable;
use CollectReviews\Helpers\Uploads;
use CollectReviews\Integrations\FormsIntegrationInterface;
use CollectReviews\ModuleInterface;
use CollectReviews\ReviewRequests\Email\Preview as ReviewRequestsEmailPreview;
use WP_Error;

/**
 * Class Settings.
 *
 * @since 1.0.0
 */
class Settings implements ModuleInterface {

	/**
	 * Ajaxable trait.
	 *
	 * @since 1.0.0
	 */
	use Ajaxable;

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		$this->register_ajax( 'save_settings' );

		$this->register_ajax( 'get_review_request_email_preview' );

		$this->register_ajax( 'get_forms_integration_forms' );
		$this->register_ajax( 'get_forms_integration_form_fields' );
	}

	/**
	 * Save settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Input data.
	 *
	 * @return array
	 */
	public function save_settings( $data ) {

		$options = collect_reviews()->get( 'options' );

		$logo_raw = $data['review_request_email']['logo_raw'] ?? [];

		if ( ! empty( $logo_raw ) && isset( $logo_raw['url'], $logo_raw['width'], $logo_raw['height'] ) ) {
			$logo_filename = $this->upload_review_request_email_logo( $logo_raw['url'] );

			// TODO: Handle error (next release).
			if ( ! is_wp_error( $logo_filename ) ) {
				$data['review_request_email']['logo'] = [
					'filename' => $logo_filename,
					'width'    => $logo_raw['width'],
					'height'   => $logo_raw['height'],
				];
			}

			unset( $data['review_request_email']['logo_raw'] );
		}

		$options->set( $data )->save();

		return $options->get_all();
	}

	/**
	 * Upload review request email logo.
	 *
	 * @since 1.0.0
	 *
	 * @param string $base64_image Base64 encoded image.
	 *
	 * @return string|WP_Error Logo filename or WP_Error.
	 */
	private function upload_review_request_email_logo( $base64_image ) {

		$upload_dir = Uploads::get_upload_dir( true );

		if ( is_wp_error( $upload_dir ) ) {
			return $upload_dir;
		}

		$base64_image  = str_replace( 'data:image/png;base64,', '', $base64_image );
		$base64_image  = str_replace( ' ', '+', $base64_image );
		$decoded_image = base64_decode( $base64_image );

		$logo_filename = 'review-request-email-logo-' . uniqid() . '.png';

		// TODO: Change to WP_Filesystem (next release).
		$uploaded = file_put_contents( $upload_dir['path'] . '/' . $logo_filename, $decoded_image );

		if ( ! $uploaded ) {
			return new WP_Error( 'bad_permissions', __( 'Could not write to file.' ) );
		}

		return $logo_filename;
	}

	/**
	 * Get review request email preview.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Input data.
	 *
	 * @return string Preview HTML.
	 */
	public function get_review_request_email_preview( $data ) {

		$logo = [];

		if (
			! empty( $data['logo_raw'] ) &&
			isset( $data['logo_raw']['url'], $data['logo_raw']['width'], $data['logo_raw']['height'] )
		) {
			$logo = [
				'url'    => esc_url_raw( $data['logo_raw']['url'], [ 'data' ] ),
				'width'  => intval( $data['logo_raw']['width'] ),
				'height' => intval( $data['logo_raw']['height'] ),
			];
		} elseif (
			! empty( $data['logo'] ) &&
			isset( $data['logo']['filename'], $data['logo']['width'], $data['logo']['height'] )
		) {
			$logo = [
				'filename' => sanitize_text_field( $data['logo']['filename'] ),
				'width'    => intval( $data['logo']['width'] ),
				'height'   => intval( $data['logo']['height'] ),
			];
		}

		$content     = isset( $data['content'] ) ? wp_kses_post( $data['content'] ) : '';
		$footer_text = isset( $data['footer_text'] ) ? wp_kses_post( $data['footer_text'] ) : '';

		$preview = new ReviewRequestsEmailPreview(
			$content,
			[
				'logo'        => $logo,
				'footer_text' => $footer_text,
			]
		);

		return $preview->get();
	}

	/**
	 * Get forms.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Input data.
	 *
	 * @return array|WP_Error Forms array or WP_Error.
	 */
	public function get_forms_integration_forms( $data ) {

		$integration_slug = isset( $data['integration'] ) ? sanitize_text_field( $data['integration'] ) : false;
		$integration      = collect_reviews()->get( 'integrations' )->get_integration( $integration_slug );

		if ( empty( $integration ) || ! $integration instanceof FormsIntegrationInterface ) {
			return new WP_Error( 'unknown_integration', __( 'Unknown integration.', 'collect-reviews' ) );
		}

		return $integration->get_forms();
	}

	/**
	 * Get form fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Input data.
	 *
	 * @return array|WP_Error Form fields array or WP_Error.
	 */
	public function get_forms_integration_form_fields( $data ) {

		$integration_slug = isset( $data['integration'] ) ? sanitize_text_field( $data['integration'] ) : false;
		$integration      = collect_reviews()->get( 'integrations' )->get_integration( $integration_slug );

		if ( empty( $integration ) || ! $integration instanceof FormsIntegrationInterface ) {
			return new WP_Error( 'unknown_integration', __( 'Unknown integration.', 'collect-reviews' ) );
		}

		$form_id = isset( $data['form_id'] ) ? intval( $data['form_id'] ) : false;

		if ( empty( $form_id ) ) {
			return new WP_Error( 'invalid_form_id', __( 'Invalid form ID.', 'collect-reviews' ) );
		}

		$field_type = isset( $data['field_type'] ) ? sanitize_key( $data['field_type'] ) : false;

		if ( ! in_array( $field_type, [ 'email', 'name' ], true ) ) {
			return new WP_Error( 'invalid_field_type', __( 'Invalid field type.', 'collect-reviews' ) );
		}

		return $integration->get_form_fields( $form_id, $field_type );
	}
}
