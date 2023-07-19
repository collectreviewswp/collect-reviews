<?php

namespace CollectReviews\Helpers;

use WP_Error;

/**
 * Class Uploads. Helper class for plugin uploads.
 *
 * @since 1.0.0
 */
class Uploads {

	/**
	 * Get plugin upload directory.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $create_dir Create directory if not exists.
	 *
	 * @return array|WP_Error
	 */
	public static function get_upload_dir( $create_dir = false ) {

		$wp_uploads = wp_upload_dir();

		if ( $wp_uploads['error'] !== false ) {
			return new WP_Error( 'upload_dir', $wp_uploads['error'] );
		}

		$plugin_upload_dir = 'collect-reviews';

		$plugin_upload_path = trailingslashit( realpath( $wp_uploads['basedir'] ) ) . $plugin_upload_dir;

		if ( $create_dir && ! file_exists( $plugin_upload_path ) && ! wp_mkdir_p( $plugin_upload_path ) ) {
			return new WP_Error(
				'upload_dir_unable_create',
				sprintf(
				/* translators: %s: plugin directory path. */
					esc_html__( 'Unable to create directory %s.', 'collect-reviews' ),
					esc_html( $plugin_upload_path )
				)
			);
		}

		return [
			'path' => $plugin_upload_path,
			'url'  => trailingslashit( $wp_uploads['baseurl'] ) . $plugin_upload_dir,
		];
	}

	/**
	 * Get plugin upload url.
	 *
	 * @since 1.0.0
	 *
	 * @return string|false
	 */
	public static function get_upload_url() {

		$upload_dir = self::get_upload_dir();

		return ! is_wp_error( $upload_dir ) ? $upload_dir['url'] : false;
	}

	/**
	 * Get plugin upload path.
	 *
	 * @return string|false
	 */
	public static function get_upload_path() {

		$upload_dir = self::get_upload_dir();

		return ! is_wp_error( $upload_dir ) ? $upload_dir['path'] : false;
	}

	/**
	 * Check if file exists in plugin upload directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename File name.
	 *
	 * @return bool
	 */
	public static function file_exists( $filename ) {

		$upload_dir = self::get_upload_dir();

		if ( is_wp_error( $upload_dir ) ) {
			return false;
		}

		return file_exists( $upload_dir['path'] . '/' . $filename );
	}

	/**
	 * Get file url in plugin upload directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename File name.
	 *
	 * @return string|false
	 */
	public static function get_file_url( $filename ) {

		$upload_dir = self::get_upload_dir();

		if ( is_wp_error( $upload_dir ) ) {
			return false;
		}

		return $upload_dir['url'] . '/' . $filename;
	}
}
