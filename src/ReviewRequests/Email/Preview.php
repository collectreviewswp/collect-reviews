<?php

namespace CollectReviews\ReviewRequests\Email;

use CollectReviews\Emails\SmartTags\Processor;
use CollectReviews\Emails\SmartTags\SiteName;
use CollectReviews\Helpers\Date;
use CollectReviews\ReviewRequests\Email\SmartTags\Action;
use CollectReviews\ReviewRequests\Email\SmartTags\RatingStars;
use CollectReviews\ReviewRequests\ReviewRequest;

/**
 * Class Preview. Renders a preview of the review request email.
 *
 * @since 1.0.0
 */
class Preview {

	/**
	 * Email content.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $content;

	/**
	 * Email arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $args;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Email content.
	 * @param array  $args    Email arguments.
	 */
	public function __construct( $content, $args = [] ) {

		$this->content = $content;
		$this->args    = $args;
	}

	/**
	 * Get email preview HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get() {

		$review_request = new ReviewRequest();
		$review_request->set_created_date( Date::create( 'now' ) );
		$review_request->set_integration( 'woocommerce' );

		$rating_stars_style = $this->args['rating_stars_style'] ?? false;

		$smart_tags = new Processor( [
			'{{site_name}}'    => new SiteName(),
			'{{rating_stars}}' => new RatingStars( $review_request, $rating_stars_style ),
			'{{action}}'       => new Action( $review_request ),
		] );

		if ( isset( $this->args['footer_text'] ) ) {
			$this->args['footer_text'] = $smart_tags->process( $this->args['footer_text'] );
		}

		$template = new Template( $smart_tags->process( $this->content ), $this->args );

		return $template->get();
	}
}
