<?php
/**
 * Review reply page logo.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CollectReviews\Helpers\Uploads;

$logo = collect_reviews()->get( 'options' )->get( 'review_request_email.logo' );

if (
	empty( $logo ) ||
	empty( $logo['filename'] )
) {
	return;
}

$logo_url = Uploads::get_file_url( $logo['filename'] );
?>

<div class="collect-reviews-page__logo">
	<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'Logo', 'collect-reviews' ); ?>">
</div>
