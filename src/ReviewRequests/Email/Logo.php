<?php

namespace CollectReviews\ReviewRequests\Email;

/**
 * Class Logo. Email logo HTML.
 *
 * @since 1.0.0
 */
class Logo {

	/**
	 * Logo URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Logo width.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $width;

	/**
	 * Logo height.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $height;

	/**
	 * Set logo URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Logo URL.
	 */
	public function set_url( $url ) {

		$this->url = $url;
	}

	/**
	 * Set logo width.
	 *
	 * @since 1.0.0
	 *
	 * @param int $width Logo width.
	 */
	public function set_width( $width ) {

		$this->width = $width;
	}

	/**
	 * Set logo height.
	 *
	 * @since 1.0.0
	 *
	 * @param int $height Logo height.
	 */
	public function set_height( $height ) {

		$this->height = $height;
	}

	/**
	 * Display logo.
	 *
	 * @since 1.0.0
	 */
	public function display() {

		if ( empty( $this->url ) ) {
			return;
		}

		printf(
			'<img src="%s" %s %s alt="%s" style="max-width:300px;max-height:150px;display:block;margin:0 auto;outline:none;border:none;text-decoration:none;" >',
			esc_url( $this->url, array_merge( wp_allowed_protocols(), [ 'data' ] ) ),
			$this->width ? 'width="' . intval( $this->width ) . '"' : '',
			$this->height ? 'height="' . intval( $this->height ) . '"' : '',
			esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) )
		);
	}
}
