<?php

namespace CollectReviews\Ajax;

/**
 * Class AjaxManager.
 *
 * @since 1.0.0
 */
class AjaxManager {

	/**
	 * The ajax action name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const ACTION = 'collect_reviews_ajax';

	/**
	 * The ajax handlers.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $handlers = [];

	/**
	 * Whether the current request is an plugin ajax request.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_self_ajax_request;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : '';

		$this->is_self_ajax_request = collect_reviews()->get( 'request' )->is_ajax() && $action === self::ACTION;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		if ( is_user_logged_in() ) {
			add_action( 'wp_ajax_' . self::ACTION, [ $this, 'handle' ] );
		} else {
			add_action( 'wp_ajax_nopriv_' . self::ACTION, [ $this, 'handle' ] );
		}
	}

	/**
	 * Register an ajax handler.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $task       The task name.
	 * @param callable $callback   The callback name.
	 * @param string   $capability The capability required to perform the task.
	 */
	public function register( $task, $callback, $capability = 'manage_options' ) {

		// Bail if not an self ajax request.
		if ( ! $this->is_self_ajax_request ) {
			return;
		}

		$this->handlers[ $task ] = [
			'callback'   => $callback,
			'capability' => $capability,
		];
	}

	/**
	 * Handle the ajax request.
	 *
	 * @since 1.0.0
	 */
	public function handle() {

		// Check nonce.
		if ( check_ajax_referer( 'collect_reviews_ajax', '_wpnonce', false ) === false ) {
			wp_send_json_error( esc_html__( 'Something went wrong. Try again later.', 'collect-reviews' ) );
		}

		if ( ! isset( $_REQUEST['task'] ) ) {
			wp_send_json_error( esc_html__( 'Missed task parameter.', 'collect-reviews' ) );
		}

		$task = sanitize_key( $_REQUEST['task'] );

		if ( ! isset( $this->handlers[ $task ] ) || ! is_callable( $this->handlers[ $task ]['callback'] ) ) {
			wp_send_json_error( esc_html__( 'Invalid task.', 'collect-reviews' ) );
		}

		if (
			! empty( $this->handlers[ $task ]['capability'] ) &&
			! current_user_can( $this->handlers[ $task ]['capability'] )
		) {
			wp_send_json_error( esc_html__( 'You do not have permission to perform this task.', 'collect-reviews' ) );
		}

		$data = isset( $_REQUEST['data'] ) ? wp_unslash( $_REQUEST['data'] ) : [];

		if ( is_string( $data ) ) {
			parse_str( $data, $data );
		}

		// Apply wp_kses_post_deep() to all data for basic sanitization.
		// Further each key will be sanitized separately in the ajax handler.
		$data = wp_kses_post_deep( $data );

		$result = call_user_func( $this->handlers[ $task ]['callback'], $data );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		if ( empty( $result ) || $result === true ) {
			$result = [];
		}

		wp_send_json_success( $result );
	}
}
