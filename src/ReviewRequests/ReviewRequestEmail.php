<?php

namespace CollectReviews\ReviewRequests;

use CollectReviews\Emails\EmailInterface;
use CollectReviews\Emails\Mailer;
use CollectReviews\Emails\SmartTags\Processor;
use CollectReviews\Emails\SmartTags\SiteName;
use CollectReviews\Emails\SmartTags\SiteUrl;
use CollectReviews\ReviewRequests\Email\SmartTags\Action;
use CollectReviews\ReviewRequests\Email\SmartTags\CustomerFirstName;
use CollectReviews\ReviewRequests\Email\SmartTags\CustomerLastName;
use CollectReviews\ReviewRequests\Email\SmartTags\RatingStars;
use CollectReviews\ReviewRequests\Email\Template;

/**
 * Class ReviewRequestEmail.
 *
 * @since 1.0.0
 */
class ReviewRequestEmail implements EmailInterface {

	/**
	 * Review request.
	 *
	 * @since 1.0.0
	 *
	 * @var ReviewRequest
	 */
	protected $review_request;

	/**
	 * Email options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Smart tags.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $smart_tags;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review request.
	 */
	public function __construct( ReviewRequest $review_request ) {

		$this->review_request = $review_request;

		$this->options = collect_reviews()->get( 'options' )->get( 'review_request_email' );

		$this->smart_tags = [
			'{{site_name}}'           => new SiteName(),
			'{{site_url}}'            => new SiteUrl(),
			'{{rating_stars}}'        => new RatingStars( $this->review_request ),
			'{{action}}'              => new Action( $this->review_request ),
			'{{customer_first_name}}' => new CustomerFirstName( $this->review_request ),
			'{{customer_last_name}}'  => new CustomerLastName( $this->review_request ),
		];
	}

	/**
	 * Send email.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function send() {

		if ( empty( $this->options['subject'] ) || empty( $this->options['content'] ) ) {
			return false;
		}

		$smart_tags  = new Processor( $this->smart_tags );
		$footer_text = $smart_tags->process( $this->options['footer_text'] );

		// TODO: Implement UI (next release).
		// Clear footer text if action smart tag is empty and present in footer text.
		if (
			strpos( $this->options['footer_text'], '{{action}}' ) !== false &&
			empty( $this->smart_tags['{{action}}']->get_value() )
		) {
			$footer_text = '';
		}

		$template = new Template(
			$smart_tags->process( $this->options['content'] ),
			[
				'logo'        => $this->options['logo'],
				'footer_text' => $footer_text,
			]
		);

		$subject = $smart_tags->process( $this->options['subject'] );

		$mailer = new Mailer();

		$mailer->set_to( $this->review_request->get_email() )
					 ->set_subject( $subject )
					 ->set_message( $template->get() );

		if ( ! empty( $this->options['from_name'] ) ) {
			$mailer->set_from_name( $this->options['from_name'] );
		}

		if ( ! empty( $this->options['from_email'] ) ) {
			$mailer->set_from_email( $this->options['from_email'] );
		}

		return $mailer->send();
	}

	/**
	 * Get default email content.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_content() {

		ob_start();
		?>
		<p>
			<?php
			printf( /* translators: %1$s - customer first name; %2$s - customer last name.  */
				esc_html__( 'Dear %1$s %2$s', 'collect-reviews' ),
				'{{customer_first_name}}',
				'{{customer_last_name}}' );
			?>,
		</p>
		<p>
			<?php
			printf( /* translators: %s - site name. */
				esc_html__( 'We are grateful that you have chosen %s.', 'collect-reviews' ),
				'{{site_name}}'
			);
			?>
		</p>
		<p><?php esc_html_e( 'Please take a moment to share your experience. Your feedback is super important to us.', 'collect-reviews' ); ?></p>
		<p><strong><?php esc_html_e( 'Click the stars to review us', 'collect-reviews' ); ?></strong></p>
		<p>{{rating_stars}}</p>
		<p><?php esc_html_e( 'It\'s only a minute for you, but a huge help for us.', 'collect-reviews' ); ?></p>
		<p>
			<?php
			echo wp_kses(
				sprintf( /* translators: %1$s - site name. */
					__( 'Thank you in advance,<br>%1$s team', 'collect-reviews' ),
					'{{site_name}}'
				),
				[ 'br' => [] ]
			);
			?>
		</p>
		<?php

		return ob_get_clean();
	}
}
