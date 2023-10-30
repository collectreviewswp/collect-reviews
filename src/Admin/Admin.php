<?php

namespace CollectReviews\Admin;

use CollectReviews\ModuleInterface;

/**
 * Class Admin.
 *
 * @since 1.0.0
 */
class Admin implements ModuleInterface {

	/**
	 * Slug of the admin area page.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SLUG = 'collect-reviews';

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		// Add the options page.
		add_action( 'admin_menu', [ $this, 'add_admin_pages' ] );

		// Add plugin action links on Plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename( COLLECT_REVIEWS_PLUGIN_FILE ),
			[ $this, 'add_plugin_action_link' ]
		);

		// Register admin pages hooks.
		if ( $this->is_admin_page() ) {
			$this->admin_pages_hooks();
		}
	}

	/**
	 * Register admin pages hooks.
	 *
	 * @since 1.0.0
	 */
	private function admin_pages_hooks() {

		// Remove all admin notices.
		add_action( 'in_admin_header', [ $this, 'remove_notices' ] );

		// Remove footer text.
		add_filter( 'admin_footer_text', '__return_empty_string' );
	}

	/**
	 * Register admin pages.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_pages() {

		// Options pages access capability.
		$access_capability = 'manage_options';

		add_menu_page(
			esc_html__( 'Collect Reviews', 'collect-reviews' ),
			esc_html__( 'Collect Reviews', 'collect-reviews' ),
			$access_capability,
			self::SLUG,
			[ $this, 'display' ],
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+CiAgICA8cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNMTMsNDYuMThoMzIuM2E0LDQsMCwwLDAsNC00VjM1LjlhNCw0LDAsMCwwLTQtNEgxM2E0LDQsMCwwLDAtNCw0VjQyLjJBNCw0LDAsMCwwLDEzLDQ2LjE4WiIvPgogICAgPHBhdGggZmlsbD0iI2E3YWFhZCIgZD0iTTEyLDM1LjlhMSwxLDAsMCwxLDEtMWgzMi4zYTEsMSwwLDAsMSwxLDFWNDIuMmExLDEsMCwwLDEtMSwxSDEzYTEsMSwwLDAsMS0xLTFaIi8+CiAgICA8cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNOSw2NGE0LDQsMCwwLDAsNCw0aDMyLjNhNCw0LDAsMCwwLDQtNFY1Ny43NWE0LDQsMCwwLDAtNC00SDEzYTQsNCwwLDAsMC00LDRaIi8+CiAgICA8cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNOSw1Ny43MWExLDEsMCwwLDEsMS0xaDMyLjNhMSwxLDAsMCwxLDEsMVY2NGExLDEsMCwwLDEtMSwxSDEzYTEsMSwwLDAsMS0xLTFaIi8+CiAgICA8cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNOSw4NmE0LDQsMCwwLDAsNCw0SDg2LjQyYTQsNCwwLDAsMCw0LTRWNzkuNzZhNCw0LDAsMCwwLTQtNEgxM2E0LDQsMCwwLDAtNCw0WiIvPgogICAgPHBhdGggZmlsbD0iI2E3YWFhZCIgZD0iTTksNzkuNzdhMSwxLDAsMCwxLDEtMUg4Ni40MmExLDEsMCwwLDEsMSwxVjg2YTEsMSwwLDAsMS0xLDFIMTNhMSwxLDAsMCwxLTEtMVoiLz4KICAgIDxwYXRoIGZpbGw9IiNhN2FhYWQiIGQ9Ik04NywxMEgxM2E0LDQsMCwwLDAtNCw0VjIwLjNhNCw0LDAsMCwwLDQsNEg4N2E0LDQsMCwwLDAsNC00VjE0QTQsNCwwLDAsMCw4NywxMFoiLz4KICAgIDxwYXRoIGZpbGw9IiNhN2FhYWQiIGQ9Ik04OCwyMC4zYTEsMSwwLDAsMS0xLDFIMTNhMSwxLDAsMCwxLTEtMVYxNGExLDEsMCwwLDEsMS0xSDg3YTEsMSwwLDAsMSwxLDFaIi8+CiAgICA8cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNNzIuMTgsMzEuOTJoMGEzLjM4LDMuMzgsMCwwLDAtMywxLjg5bC00LDgtOC44OCwxLjI5YTMuNCwzLjQsMCwwLDAtMS44OCw1LjhsNi40Myw2LjI3TDU5LjMsNjQuMDZhMy40LDMuNCwwLDAsMCw0LjkzLDMuNThsNy45NS00LjE4LDcuOTUsNC4xOGEzLjQsMy40LDAsMCwwLDQuOTMtMy41OGwtMS41Mi04Ljg1TDkwLDQ4Ljk1YTMuNCwzLjQsMCwwLDAtMS44OC01LjhMNzkuMiw0MS44NmwtNC04aDBBMy4zOCwzLjM4LDAsMCwwLDcyLjE4LDMxLjkyWiIvPgo8L3N2Zz4K',
			100
		);

		add_submenu_page(
			self::SLUG,
			esc_html__( 'Requests', 'collect-reviews' ),
			esc_html__( 'Requests', 'collect-reviews' ),
			$access_capability,
			self::SLUG,
			[ $this, 'display' ]
		);

		add_submenu_page(
			self::SLUG,
			esc_html__( 'Settings', 'collect-reviews' ),
			esc_html__( 'Settings', 'collect-reviews' ),
			$access_capability,
			self::SLUG . '-settings',
			[ $this, 'display' ]
		);
	}

	/**
	 * Add plugin action links on Plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Existing plugin action links.
	 *
	 * @return array
	 */
	public function add_plugin_action_link( $links ) {

		$custom['settings'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			esc_url( $this->get_admin_page_url( 'settings' ) ),
			esc_attr__( 'Go to Collect Reviews Settings page', 'collect-reviews' ),
			esc_html__( 'Settings', 'collect-reviews' )
		);

		return array_merge( $custom, (array) $links );
	}

	/**
	 * Display admin page.
	 *
	 * @since 1.0.0
	 */
	public function display() {

		// TODO: And error handling (next release).
		?>
		<style>
			.collect-reviews-overlay {
				width: 100%;
				height: calc(100vh - 32px);
				display: flex;
				align-items: center;
				justify-content: center;
			}

			/* TODO: check loader browsers compatibility (next release). */
			.collect-reviews-overlay__loader {
				--s: 40px;
				--v1: transparent, #000 0.5deg 108deg, #0000 109deg;
				--v2: transparent, #000 0.5deg 36deg, #0000 37deg;
				height: calc(var(--s) * 0.9);
				width: calc(var(--s) * 5);
				-webkit-mask: conic-gradient(from 54deg at calc(var(--s) * 0.68) calc(var(--s) * 0.57), var(--v1)), conic-gradient(from 90deg at calc(var(--s) * 0.02) calc(var(--s) * 0.35), var(--v2)), conic-gradient(from 126deg at calc(var(--s) * 0.5) calc(var(--s) * 0.7), var(--v1)), conic-gradient(from 162deg at calc(var(--s) * 0.5) 0, var(--v2));
				-webkit-mask-size: var(--s) var(--s);
				-webkit-mask-composite: xor, destination-over;
				mask-composite: exclude, add;
				-webkit-mask-repeat: repeat-x;
				background: linear-gradient(#FFD700 0 0) left/0% 100% #E4E4ED no-repeat;
				animation: collect-reviews-loader-animation 2s infinite linear;
			}

			@keyframes collect-reviews-loader-animation {
				90%, 100% {
					background-size: 100% 100%
				}
			}
		</style>

		<div id="collect-reviews-root">
			<div class="collect-reviews-overlay">
				<div class="collect-reviews-overlay__loader"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Remove all notices on plugin's admin pages.
	 *
	 * @since 1.0.0
	 */
	public function remove_notices() {

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'network_admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		remove_all_actions( 'user_admin_notices' );
	}

	/**
	 * Check if current page is plugin's admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_admin_page() {

		if ( ! collect_reviews()->get( 'request' )->is_admin() ) {
			return false;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';

		return substr( $page, 0, 15 ) === self::SLUG;
	}

	/**
	 * Get plugin admin page URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $page Page slug.
	 *
	 * @return string
	 */
	public function get_admin_page_url( $page ) {

		$page_slug = self::SLUG;

		if ( $page !== 'review-requests' ) {
			$page_slug .= '-' . $page;
		}

		return add_query_arg(
			'page',
			$page_slug,
			admin_url( 'admin.php' )
		);
	}
}
