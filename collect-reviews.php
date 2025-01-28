<?php
/**
 * Plugin Name: Collect Reviews
 * Version: 1.1.2
 * Requires at least: 5.3
 * Requires PHP: 7.2
 * Description: The ultimate WordPress plugin for automatically collecting reviews on any platform like Google or Facebook.
 * Author: Collect Reviews WP
 * Author URI: https://collectreviewswp.com/
 * Network: false
 * Text Domain: collect-reviews
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'COLLECT_REVIEWS_PLUGIN_VER' ) ) {
	define( 'COLLECT_REVIEWS_PLUGIN_VER', '1.1.2' );
}
if ( ! defined( 'COLLECT_REVIEWS_PHP_VER' ) ) {
	define( 'COLLECT_REVIEWS_PHP_VER', '7.2' );
}
if ( ! defined( 'COLLECT_REVIEWS_WP_VER' ) ) {
	define( 'COLLECT_REVIEWS_WP_VER', '5.3' );
}
if ( ! defined( 'COLLECT_REVIEWS_PLUGIN_FILE' ) ) {
	define( 'COLLECT_REVIEWS_PLUGIN_FILE', __FILE__ );
}

if ( ! function_exists( 'collect_reviews_unsupported_php_version_notice' ) ) {
	/**
	 * Display admin notice, if the server is using old/insecure PHP version.
	 *
	 * @since 1.0.0
	 */
	function collect_reviews_unsupported_php_version_notice() {

		?>
		<div class="notice notice-error">
			<p>
				<?php
				sprintf(
					wp_kses( /* translators: %s - Minimal supported PHP version. */
						__( 'Your site is running an <strong>outdated version</strong> of PHP that is no longer supported. Please contact your web hosting provider to update your PHP version to <strong>%s</strong> or higher.', 'collect-reviews' ),
						[
							'strong' => [],
						]
					),
					COLLECT_REVIEWS_PHP_VER
				);
				?>
				<br>
				<?php
				echo wp_kses(
					__( '<strong>Collect Reviews plugin is disabled</strong> on your site until you fix the issue.', 'collect-reviews' ),
					[
						'a'      => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
						'strong' => [],
					]
				);
				?>
			</p>
		</div>

		<?php

		// In case this is on plugin activation.
		if ( isset( $_GET['activate'] ) ) { //phpcs:ignore
			unset( $_GET['activate'] ); //phpcs:ignore
		}
	}
}

if ( ! function_exists( 'collect_reviews_unsupported_wp_version_notice' ) ) {
	/**
	 * Display admin notice, if the site is using unsupported WP version.
	 *
	 * @since 1.0.0
	 */
	function collect_reviews_unsupported_wp_version_notice() {

		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					wp_kses( /* translators: %s The minimal WP version supported by Collect Reviews. */
						__( 'Your site is running an <strong>old version</strong> of WordPress that is no longer supported by Collect Reviews. Please update your WordPress site to at least version <strong>%s</strong>.', 'collect-reviews' ),
						[
							'strong' => [],
						]
					),
					esc_html( COLLECT_REVIEWS_WP_VER )
				);
				?>
				<br><br>
				<?php
				echo wp_kses(
					__( '<strong>Collect Reviews plugin is disabled</strong> on your site until WordPress is updated to the required version.', 'collect-reviews' ),
					[
						'strong' => [],
					]
				);
				?>
			</p>
		</div>

		<?php

		// In case this is on plugin activation.
		if ( isset( $_GET['activate'] ) ) { //phpcs:ignore
			unset( $_GET['activate'] ); //phpcs:ignore
		}
	}
}

/**
 * Display admin notice and prevent plugin code execution, if the server is
 * using old/insecure PHP version.
 *
 * @since 1.0.0
 */
if ( version_compare( phpversion(), COLLECT_REVIEWS_PHP_VER, '<' ) ) {
	add_action( 'admin_notices', 'collect_reviews_unsupported_php_version_notice' );

	return;
}

/**
 * Display admin notice and prevent plugin code execution, if the WP version is lower than COLLECT_REVIEWS_WP_VER.
 *
 * @since 1.0.0
 */
if ( version_compare( get_bloginfo( 'version' ), COLLECT_REVIEWS_WP_VER, '<' ) ) {
	add_action( 'admin_notices', 'collect_reviews_unsupported_wp_version_notice' );

	return;
}

// Prevent double loading of the same plugin.
if ( class_exists( 'CollectReviews\Core' ) ) {
	return;
}

require_once 'vendor/autoload.php';

if ( ! function_exists( 'collect_reviews' ) ) {
	/**
	 * Global function-holder. Works similar to a singleton's instance().
	 *
	 * @since 1.0.0
	 *
	 * @return \CollectReviews\Core
	 */
	function collect_reviews() {

		static $core;

		if ( ! isset( $core ) ) {
			$core = new \CollectReviews\Core();
		}

		return $core;
	}
}

// Run the universe!
collect_reviews();
