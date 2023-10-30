<?php
/**
 * Review reply page header.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>
<body>
<main class="collect-reviews-page" id="collect-reviews">

<?php collect_reviews()->get( 'templates' )->display_template( 'review-replies/logo' ); ?>
