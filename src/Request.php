<?php

namespace CollectReviews;

/**
 * Class Request.
 *
 * This class is used to determine the type of the current request.
 *
 * @since 1.0.0
 */
class Request {

	/**
	 * Whether the current request is a frontend request.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_frontend = false;

	/**
	 * Whether the current request is an admin request.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_admin = false;

	/**
	 * Whether the current request is an AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_ajax = false;

	/**
	 * Whether the current request is a cron request.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_cron = false;

	/**
	 * Whether the current request is a REST API request.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_rest = false;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( wp_doing_ajax() ) {
			$this->is_ajax = true;
		} elseif ( wp_doing_cron() ) {
			$this->is_cron = true;
		} elseif ( is_admin() ) {
			$this->is_admin = true;
		} elseif ( $this->wp_doing_rest() ) {
			$this->is_rest = true;
		} else {
			$this->is_frontend = true;
		}
	}

	/**
	 * Whether the current request is an AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_ajax() {

		return $this->is_ajax;
	}

	/**
	 * Whether the current request is a cron request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_cron() {

		return $this->is_cron;
	}

	/**
	 * Whether the current request is an admin request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_admin() {

		return $this->is_admin;
	}

	/**
	 * Whether the current request is a REST API request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_rest() {

		return $this->is_rest;
	}

	/**
	 * Whether the current request is a frontend request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_frontend() {

		return $this->is_frontend;
	}

	/**
	 * Returns true if the request is REST API request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function wp_doing_rest() {

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) !== false;
	}
}
