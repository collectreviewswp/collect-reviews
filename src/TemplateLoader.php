<?php

namespace CollectReviews;

/**
 * Class TemplateLoader.
 *
 * Loads templates from the plugin's templates folder.
 *
 * @since 1.0.0
 */
class TemplateLoader {

	/**
	 * Display the template contents.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template name.
	 * @param array $data Template data.
	 *
	 * @return void|false Void on success, false if the template does not exist.
	 */
	public function display_template( $template, $data = [] ) {

		$path = $this->get_template_path( $template );

		if ( ! $path ) {
			return false;
		}

		require $path;
	}

	/**
	 * Get the template path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template name.
	 *
	 * @return bool|string
	 */
	public function get_template_path( $template ) {

		$path = collect_reviews()->get_plugin_path() . '/templates/' . $template . '.php';

		if ( ! file_exists( $path ) ) {
			return false;
		}

		return $path;
	}
}
