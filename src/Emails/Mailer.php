<?php

namespace CollectReviews\Emails;

/**
 * Class AbstractEmail.
 *
 * @since 1.0.0
 */
class Mailer {

	/**
	 * To email address.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $to;

	/**
	 * Reply-to email address.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $reply_to;

	/**
	 * Email subject.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $subject;

	/**
	 * Email message.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $message;

	/**
	 * Email from name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $from_name;

	/**
	 * Email from email address.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $from_email;

	/**
	 * Whether email is HTML or plain text.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_html = true;

	/**
	 * Set the to email address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $to To email address.
	 */
	public function set_to( $to ) {

		$this->to = $to;

		return $this;
	}

	/**
	 * Set the reply-to email address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $reply_to Reply-to email address.
	 */
	public function set_reply_to( $reply_to ) {

		$this->reply_to = $reply_to;

		return $this;
	}

	/**
	 * Set the email subject.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subject Email subject.
	 */
	public function set_subject( $subject ) {

		$this->subject = $subject;

		return $this;
	}

	/**
	 * Set the email message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Email message.
	 */
	public function set_message( $message ) {

		$this->message = $message;

		return $this;
	}

	/**
	 * Set whether email is HTML or plain text.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $is_html Whether email is HTML or plain text.
	 */
	public function set_is_html( $is_html ) {

		$this->is_html = $is_html;

		return $this;
	}

	/**
	 * Set the from name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $from_name From name.
	 */
	public function set_from_name( $from_name ) {

		$this->from_name = $from_name;
	}

	/**
	 * Set the from email address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $from_email From email address.
	 */
	public function set_from_email( $from_email ) {

		$this->from_email = $from_email;
	}

	/**
	 * Get the from name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $from_name From name.
	 *
	 * @return string
	 */
	public function get_from_name( $from_name = '' ) {

		if ( ! empty( $this->from_name ) ) {
			$from_name = wp_specialchars_decode( esc_html( $this->from_name ), ENT_QUOTES );
		}

		return $from_name;
	}

	/**
	 * Get the from email address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $from_email From email address.
	 *
	 * @return string
	 */
	public function get_from_email( $from_email = '' ) {

		return ! empty( $this->from_email ) ? sanitize_email( $this->from_email ) : $from_email;
	}

	/**
	 * Get email content type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_content_type() {

		return $this->is_html ? 'text/html' : 'text/plain';
	}

	/**
	 * Send email.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function send() {

		add_filter( 'wp_mail_from', [ $this, 'get_from_email' ] );
		add_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );
		add_filter( 'wp_mail_content_type', [ $this, 'get_content_type' ] );

		$headers = [];

		if ( ! empty( $this->reply_to ) ) {
			$headers[] = 'Reply-To: ' . $this->reply_to;
		}

		$is_sent = wp_mail( $this->to, $this->subject, $this->message, $headers );

		remove_filter( 'wp_mail_from', [ $this, 'get_from_email' ] );
		remove_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );
		remove_filter( 'wp_mail_content_type', [ $this, 'get_content_type' ] );

		return $is_sent;
	}
}
