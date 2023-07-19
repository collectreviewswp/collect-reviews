<?php

namespace CollectReviews\ReviewReplies;

use CollectReviews\Ajax\Ajaxable;
use CollectReviews\Emails\Mailer;
use CollectReviews\Helpers\Date;
use CollectReviews\ModuleInterface;
use CollectReviews\ReviewRequests\ReviewRequest;
use WP_Error;

/**
 * Class ReviewReplyPage.
 *
 * This is the page to which the user is redirected after clicking on the review link in the email.
 *
 * @since 1.0.0
 */
class ReviewReplyPage implements ModuleInterface {

	/**
	 * Ajaxable trait.
	 *
	 * @since 1.0.0
	 */
	use Ajaxable;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'template_redirect', [ $this, 'handle_review_reply' ] );

		$this->register_ajax( 'positive_review_link_click', false, false );

		$this->register_ajax( 'review_form_submit', 'negative_review_form_submit', false );
	}

	/**
	 * Page hooks.
	 *
	 * @since 1.0.0
	 */
	private function page_hooks() {

		add_filter( 'document_title_parts', [ $this, 'page_title' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Change page title.
	 *
	 * @since 1.0.0
	 *
	 * @param array $title Original page title.
	 *
	 * @return array
	 */
	public function page_title( $title ) {

		$title['title'] = esc_html__( 'Review', 'collect-reviews' );

		return $title;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style(
			'collect-reviews',
			collect_reviews()->get_plugin_url() . '/assets/css/review-replies.css',
			[],
			COLLECT_REVIEWS_PLUGIN_VER
		);

		wp_enqueue_script(
			'collect-reviews',
			collect_reviews()->get_plugin_url() . '/assets/js/review-replies.js',
			[ 'jquery' ],
			COLLECT_REVIEWS_PLUGIN_VER,
			true
		);

		wp_localize_script(
			'collect-reviews',
			'collect_reviews',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			]
		);
	}

	/**
	 * Handle review reply.
	 *
	 * @since 1.0.0
	 */
	public function handle_review_reply() {

		if (
			! isset( $_GET['collect_reviews_review_replay'] ) ||
			! isset( $_GET['review_request_id'] ) ||
			! isset( $_GET['review_request_key'] ) ||
			! isset( $_GET['rating'] )
		) {
			return;
		}

		$review_request_id = intval( $_GET['review_request_id'] );
		$review_request    = new ReviewRequest( $review_request_id );

		if (
			$review_request->get_id() === 0 ||
			$review_request->get_key() !== $_GET['review_request_key']
		) {
			wp_redirect( home_url() );
			exit();
		}

		$rating = intval( $_GET['rating'] );

		$review_request->set_rating( $rating );
		$review_request->set_rate_date( Date::create( 'now' ) );
		$review_request->save();

		$options                   = collect_reviews()->get( 'options' );
		$negative_review_threshold = $options->get( 'negative_review.threshold', 3 );

		if ( $rating > $negative_review_threshold ) {
			$template = 'review-replies/positive-review';
		} else {
			$template = 'review-replies/negative-review';
		}

		set_query_var( 'collect_reviews_review_request', $review_request );

		nocache_headers();

		$this->page_hooks();

		add_filter( 'template_include', function () use ( $template ) {
			return collect_reviews()->get( 'templates' )->get_template_path( $template );
		} );
	}

	/**
	 * Handle positive review link click.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data.
	 *
	 * @return bool|WP_Error
	 */
	public function positive_review_link_click( $data ) {

		if (
			empty( $data['review_request_id'] ) ||
			empty( $data['review_request_key'] )
		) {
			return new WP_Error( 'missed_required_params', esc_html__( 'Missed required parameters.', 'collect-reviews' ) );
		}

		$review_request_id = intval( $data['review_request_id'] );
		$review_request    = new ReviewRequest( $review_request_id );

		if (
			$review_request->get_id() === 0 ||
			$review_request->get_key() !== $data['review_request_key']
		) {
			return new WP_Error( 'invalid_review_request', esc_html__( 'Invalid review request.', 'collect-reviews' ) );
		}

		$review_request->set_positive_review_link_clicked( true );
		$review_request->save();

		return true;
	}

	/**
	 * Handle negative review form submit.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data.
	 *
	 * @return bool|WP_Error
	 */
	public function negative_review_form_submit( $data ) {

		if (
			empty( $data['review_request_id'] ) ||
			empty( $data['review_request_key'] ) ||
			empty( $data['name'] ) ||
			empty( $data['email'] ) ||
			empty( $data['comment'] )
		) {
			return new WP_Error( 'missed_required_params', esc_html__( 'Missed required parameters.', 'collect-reviews' ) );
		}

		$review_request_id = intval( $data['review_request_id'] );
		$review_request    = new ReviewRequest( $review_request_id );

		if (
			$review_request->get_id() === 0 ||
			$review_request->get_key() !== $data['review_request_key']
		) {
			return new WP_Error( 'invalid_review_request', esc_html__( 'Invalid review request.', 'collect-reviews' ) );
		}

		$customer_name  = sanitize_textarea_field( $data['name'] );
		$customer_email = sanitize_email( $data['email'] );
		$review_content = sanitize_textarea_field( $data['comment'] );

		$options  = collect_reviews()->get( 'options' );
		$to_email = $options->get( 'negative_review.email', get_option( 'admin_email' ) );

		$subject = sprintf( /* translators: %s blog name. */
			esc_html__( '[%s] New negative review received', 'collect-reviews' ),
			esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) )
		);

		$message = sprintf( /* translators: %s name. */
			esc_html__( 'Name: %s', 'collect-reviews' ),
			esc_html( $customer_name )
		);

		$message .= "\r\n";

		$message .= sprintf( /* translators: %s email address. */
			esc_html__( 'Email: %s', 'collect-reviews' ),
			esc_html( $customer_email )
		);

		$message .= "\r\n";

		$message .= sprintf( /* translators: %s rating. */
			esc_html__( 'Rating: %s', 'collect-reviews' ),
			esc_html( $review_request->get_rating() )
		);

		$message .= "\r\n";

		$message .= esc_html__( 'Comment:', 'collect-reviews' );

		$message .= "\r\n";

		$message .= $review_content;

		$message .= "\r\n";
		$message .= "\r\n";

		$message .= esc_html__( 'Received from Collect Reviews plugin.', 'collect-reviews' );

		$mailer = new Mailer();

		$mailer->set_to( $to_email )
					 ->set_subject( $subject )
					 ->set_message( $message )
					 ->set_reply_to( $customer_email )
					 ->set_is_html( false );

		$is_sent = $mailer->send();

		$review_request->set_negative_review_submitted( true );
		$review_request->set_meta( 'negative_review_email_is_sent', $is_sent );
		$review_request->save();

		return true;
	}
}
