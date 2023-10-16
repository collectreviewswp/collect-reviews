<?php

namespace CollectReviews;

use CollectReviews\Helpers\Collection;
use CollectReviews\ReviewRequests\ReviewRequestEmail;

/**
 * Class Options.
 *
 * Plugin options.
 *
 * @since 1.0.0
 */
class Options {

	/**
	 * Option name in the `options` DB table.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_NAME = 'collect_reviews';

	/**
	 * Options data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->populate();
	}

	/**
	 * Populate plugin options.
	 *
	 * @since 1.0.0
	 */
	private function populate() {

		$options = get_option( self::OPTION_NAME, [] );

		$this->options = ! empty( $options ) && is_array( $options ) ? $options : [];
	}

	/**
	 * Get all options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_all() {

		return $this->options;
	}

	/**
	 * Get option by key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {

		return Collection::get( $this->options, $key, $default );
	}

	/**
	 * Save options to DB.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data  Options data.
	 * @param bool  $merge Whether to merge with existing options or overwrite all option with provided.
	 *
	 * @return bool
	 */
	public function update( $data, $merge = true ) {

		$data = $this->process( $data );

		if ( $merge ) {
			$data = $this->merge_options( $this->get_all(), $data );
		}

		$this->options = $data;

		return update_option( self::OPTION_NAME, $this->options );
	}

	/**
	 * Process options data. Clean and set default values if needed.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Options data.
	 *
	 * @return array
	 */
	private function process( $data ) {

		Collection::walk_recursive( $data, [ $this, 'process_option' ] );

		return $data;
	}

	/**
	 * Process option. Clean and set default value if needed.
	 *
	 * This method is callback for `Collection::walk_recursive`.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value Option value. Passed by reference.
	 * @param string $key   Option key.
	 */
	public function process_option( &$value, $key ) {

		switch ( $key ) {
			case 'review_request_email.logo.width':
			case 'review_request_email.logo.height':
			case 'review_request.frequency':
			case 'negative_review.threshold':
			case 'integrations.woocommerce.triggers.review_request_delay':
			case 'integrations.easy_digital_downloads.triggers.review_request_delay':
			case 'integrations.wpforms.triggers.form.id':
			case 'integrations.wpforms.triggers.form_email_field.id':
			case 'integrations.wpforms.triggers.form_name_field.id':
			case 'integrations.wpforms.triggers.review_request_delay':
				$value = intval( $value );
				break;
			case 'review_request_email.from_name':
			case 'review_request_email.subject':
			case 'review_request_email.logo.filename':
			case 'integrations.woocommerce.triggers.platforms.name':
			case 'integrations.easy_digital_downloads.triggers.platforms.name':
			case 'integrations.wpforms.triggers.form.title':
			case 'integrations.wpforms.triggers.form_email_field.title':
			case 'integrations.wpforms.triggers.form_name_field.title':
			case 'integrations.wpforms.triggers.platforms.name':
				$value = sanitize_text_field( $value );
				break;
			case 'review_request_email.from_email':
			case 'negative_review.email':
				$value = sanitize_email( $value );
				break;
			case 'review_request_email.content':
			case 'review_request_email.footer_text':
				$value = wp_kses_post( $value );
				break;
			case 'integrations.woocommerce.enabled':
			case 'integrations.easy_digital_downloads.enabled':
			case 'integrations.wpforms.enabled':
				$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				break;
			case 'integrations.woocommerce.triggers.order_status':
			case 'integrations.woocommerce.triggers.platforms.type':
			case 'integrations.easy_digital_downloads.triggers.order_status':
			case 'integrations.easy_digital_downloads.triggers.platforms.type':
			case 'integrations.wpforms.triggers.platforms.type':
				$value = sanitize_key( $value );
				break;
			case 'integrations.woocommerce.triggers.platforms.review_url':
			case 'integrations.easy_digital_downloads.triggers.platforms.review_url':
			case 'integrations.wpforms.triggers.platforms.review_url':
				$value = esc_url_raw( $value );
				break;
			case 'integrations.wpforms.triggers.form':
			case 'integrations.wpforms.triggers.form_email_field':
			case 'integrations.wpforms.triggers.form_name_field':
				$value = ! empty( $value ) ? $value : null;
				break;
			default:
				$value = wp_kses_post( $value );
		}
	}

	/**
	 * Merge options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $old_options Old options.
	 * @param array $new_options New options.
	 *
	 * @return array
	 */
	private function merge_options( $old_options, $new_options ) {

		$merged = $old_options;

		foreach ( $new_options as $key => $value ) {
			if ( is_array( $value ) && isset ( $merged [ $key ] ) && is_array( $merged [ $key ] ) ) {
				$merged [ $key ] = $this->merge_options( $merged [ $key ], $value );
			} else {
				$merged [ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * Get default options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_default() {

		$blog_name   = get_option( 'blogname' );
		$admin_email = get_option( 'admin_email' );

		return [
			'review_request'       => [
				'frequency' => - 1
			],
			'review_request_email' => [
				'from_name'   => $blog_name,
				'from_email'  => $admin_email,
				'subject'     => sprintf( /* translators: %s blog name. */
					esc_html__( 'Tell us about your experience with %s', 'collect-reviews' ),
					'{{site_name}}'
				),
				'logo'        => false,
				'content'     => ReviewRequestEmail::get_default_content(),
				'footer_text' => sprintf( /* translators: %1$s customer action; %2$s blog name. */
					esc_html__( 'You received this email because you %1$s on %2$s website.', 'collect-reviews' ),
					'{{action}}',
					'{{site_name}}'
				)
			],
			'negative_review'      => [
				'email'     => $admin_email,
				'threshold' => 3
			],
			'integrations'         => [
				'woocommerce'            => [
					'enabled'  => true,
					'triggers' => [
						[
							'order_status'         => 'completed',
							'review_request_delay' => DAY_IN_SECONDS * 14,
							'platforms'            => [
								[
									'type'       => 'google',
									'name'       => esc_html__( 'Google', 'collect-reviews' ),
									'review_url' => '',
								]
							]
						]
					]
				],
				'easy_digital_downloads' => [
					'enabled'  => true,
					'triggers' => [
						[
							'order_status'         => 'complete',
							'review_request_delay' => DAY_IN_SECONDS * 14,
							'platforms'            => [
								[
									'type'       => 'google',
									'name'       => esc_html__( 'Google', 'collect-reviews' ),
									'review_url' => '',
								]
							]
						]
					]
				],
				'wpforms'                => [
					'enabled'  => true,
					'triggers' => [
						[
							'form'                 => null,
							'form_email_field'     => null,
							'form_name_field'      => null,
							'review_request_delay' => DAY_IN_SECONDS * 14,
							'platforms'            => [
								[
									'type'       => 'google',
									'name'       => esc_html__( 'Google', 'collect-reviews' ),
									'review_url' => '',
								]
							]
						]
					]
				]
			]
		];
	}
}
