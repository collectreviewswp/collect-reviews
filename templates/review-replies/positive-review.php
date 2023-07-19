<?php
/**
 * Positive review view.
 *
 * @since 1.0.0
 */

use CollectReviews\ReviewRequests\ReviewRequest;

$review_request = get_query_var( 'collect_reviews_review_request' );

if ( empty( $review_request ) || ! $review_request instanceof ReviewRequest ) {
	return;
}
?>

<?php collect_reviews()->get( 'templates' )->display_template( 'review-replies/header' ); ?>

<div class="collect-reviews-review">
	<p class="collect-reviews-review__text">
		<?php printf( /* translators: %s platform name. */
			esc_html__( 'We appreciate your feedback! It would mean a lot to us if you could share your experience on %s.', 'collect-reviews' ),
			esc_html( $review_request->get_platform_name() )
		);
		?>
	</p>

	<a href="<?php echo esc_url( $review_request->get_positive_review_url() ); ?>" class="collect-reviews-button collect-reviews-review__link" data-review-request-id="<?php echo esc_attr( $review_request->get_id() ); ?>" data-review-request-key="<?php echo esc_attr( $review_request->get_key() ); ?>">
		<?php
		printf( /* translators: %s platform name. */
			esc_html__( 'Rate us on %s', 'collect-reviews' ),
			esc_html( $review_request->get_platform_name() )
		);
		?>
	</a>
</div>

<?php collect_reviews()->get( 'templates' )->display_template( 'review-replies/footer' ); ?>
