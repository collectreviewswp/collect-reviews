<?php
/**
 * Negative review form.
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

<div class="collect-reviews-feedback">
	<form class="collect-reviews-feedback__form">
		<p class="collect-reviews-feedback__text">
			<?php esc_html_e( 'We appreciate your feedback! It would mean a lot to us if you could share your experience by providing a comment.', 'collect-reviews' ); ?>
		</p>

		<div class="collect-reviews-field">
			<label for="name" class="collect-reviews-field__label">
				<?php esc_html_e( 'Name', 'collect-reviews' ); ?>
			</label>
			<input type="text" name="name" id="name" class="collect-reviews-field__input" value="<?php echo esc_attr( $review_request->get_full_name() ); ?>" required>
		</div>

		<div class="collect-reviews-field">
			<label for="email" class="collect-reviews-field__label">
				<?php esc_html_e( 'Email', 'collect-reviews' ); ?>
			</label>
			<input type="email" name="email" class="collect-reviews-field__input" value="<?php echo esc_attr( $review_request->get_email() ); ?>" required>
		</div>

		<div class="collect-reviews-field">
			<label for="comment" class="collect-reviews-field__label">
				<?php esc_html_e( 'Comment', 'collect-reviews' ); ?>
			</label>
			<textarea name="comment" class="collect-reviews-field__input" rows="6" required></textarea>
		</div>

		<input type="hidden" name="review_request_id" value="<?php echo esc_attr( $review_request->get_id() ); ?>">
		<input type="hidden" name="review_request_key" value="<?php echo esc_attr( $review_request->get_key() ); ?>">

		<button type="submit" class="collect-reviews-button">
			<?php esc_html_e( 'Submit review', 'collect-reviews' ); ?>
		</button>

		<div class="collect-reviews-feedback__error"></div>
	</form>

	<div class="collect-reviews-feedback__success" style="display: none;">
		<?php esc_html_e( 'Thank you for providing your feedback!', 'collect-reviews' ); ?>
	</div>
</div>

<?php collect_reviews()->get( 'templates' )->display_template( 'review-replies/footer' ); ?>
