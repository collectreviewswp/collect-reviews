<?php

namespace CollectReviews\Admin;

use CollectReviews\Integrations\EcommerceIntegrationInterface;
use CollectReviews\ModuleInterface;

/**
 * Class Scripts.
 *
 * @since 1.0.0
 */
class Scripts implements ModuleInterface {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		if ( ! collect_reviews()->get( 'admin' )->is_admin_page() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		// TODO: move to variable (next release).
		$assets_manifest_path = collect_reviews()->get_plugin_path() . '/assets/app/asset-manifest.json';
		$assets_manifest      = json_decode( file_get_contents( $assets_manifest_path ), true );

		if ( empty( $assets_manifest ) ) {
			return;
		}

		$script_handle = '';

		foreach ( $assets_manifest['entrypoints'] as $key => $asset ) {
			$ext  = pathinfo( $asset, PATHINFO_EXTENSION );
			$path = collect_reviews()->get_plugin_url() . '/assets/app/' . $asset;
			if ( $ext === 'css' ) {
				wp_enqueue_style( 'react-css-' . $key, $path, [], COLLECT_REVIEWS_PLUGIN_VER );
			} else if ( $ext === 'js' ) {
				wp_enqueue_script( 'react-js-' . $key, $path, [ 'wp-i18n' ], COLLECT_REVIEWS_PLUGIN_VER, true );

				if ( empty( $script_handle ) ) {
					$script_handle = 'react-js-' . $key;
				}
			}
		}

		$integrations = array_map(
			function ( $integration ) {
				$data = [
					'slug'          => $integration->get_slug(),
					'title'         => $integration->get_title(),
					'is_available'  => $integration->is_available(),
					'is_enabled'    => $integration->is_enabled(),
					'is_configured' => $integration->is_configured(),
				];

				if ( $integration instanceof EcommerceIntegrationInterface ) {
					$data['order_statuses'] = $integration->get_order_statuses();
				}

				return $data;
			},
			collect_reviews()->get( 'integrations' )->get_integrations()
		);

		$data = [
			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'plugin_url'               => collect_reviews()->get_plugin_url(),
			'options'                  => collect_reviews()->get( 'options' )->get_all(),
			'config'                   => collect_reviews()->get( 'config' )->get_all(),
			'page'                     => $_GET['page'] ?? '',
			'integrations'             => array_values( $integrations ),
			'review_requests_page_url' => collect_reviews()->get( 'admin' )->get_admin_page_url( 'review-requests' ),
		];

		wp_localize_script( $script_handle, 'collectReviews', $data );
	}
}
