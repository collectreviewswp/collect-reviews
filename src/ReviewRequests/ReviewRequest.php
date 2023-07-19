<?php

namespace CollectReviews\ReviewRequests;

use CollectReviews\Helpers\Date;
use DateTime;
use DateTimeZone;

/**
 * Class ReviewRequest.
 *
 * @since 1.0.0
 */
class ReviewRequest {

	/**
	 * Pending status. The review request has been created but the email was not sent yet.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	const STATUS_PENDING = 0;

	/**
	 * Sent status. The review request email has been sent.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	const STATUS_SENT = 1;

	/**
	 * Failed status. The review request email failed to be sent.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	const STATUS_FAILED = 2;

	/**
	 * The ID.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id = 0;

	/**
	 * The unique key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The status.
	 *
	 * @since 1.0.0
	 *
	 * @var
	 */
	private $status;

	/**
	 * The email. Review request will be sent to this email address.
	 *
	 * @since 1.0.0
	 *
	 * @var
	 */
	private $email;

	/**
	 * Created date.
	 *
	 * @since 1.0.0
	 *
	 * @var DateTime
	 */
	private $created_date;

	/**
	 * Send date.
	 *
	 * @since 1.0.0
	 *
	 * @var DateTime
	 */
	private $send_date;

	/**
	 * Integration slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $integration;

	/**
	 * Platform type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $platform_type;

	/**
	 * Platform name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $platform_name;

	/**
	 * Platform review URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $positive_review_url;

	/**
	 * Meta data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $meta = null;

	/**
	 * Whether the metadata has changed.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $meta_changed = false;

	/**
	 * Data store.
	 *
	 * @since 1.0.0
	 *
	 * @var ReviewRequestsDataStore
	 */
	private $data_store;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int|null $id The ID.
	 */
	public function __construct( $id = null ) {

		$this->data_store = new ReviewRequestsDataStore();

		if ( is_numeric( $id ) && $id > 0 ) {
			$this->id = (int) $id;
			$this->data_store->read( $this );
		}
	}

	/**
	 * Get the ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_id() {

		return $this->id;
	}

	/**
	 * Set the ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The ID.
	 *
	 * @return void
	 */
	public function set_id( int $id ) {

		$this->id = (int) $id;
	}

	/**
	 * Get the unique key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_key() {

		return $this->key;
	}

	/**
	 * Set the unique key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The unique key.
	 */
	public function set_key( $key ) {

		$this->key = $key;
	}

	/**
	 * Get the status.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_status() {

		return $this->status;
	}

	/**
	 * Set the status.
	 *
	 * @since 1.0.0
	 *
	 * @param int $status The status.
	 *
	 * @return void
	 */
	public function set_status( $status ) {

		$this->status = (int) $status;
	}

	/**
	 * Get the email.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_email() {

		return $this->email;
	}

	/**
	 * Set the email.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email The email.
	 */
	public function set_email( $email ) {

		$this->email = $email;
	}

	/**
	 * Get the created date.
	 *
	 * @since 1.0.0
	 *
	 * @return DateTime Returns the DateTime object in UTC timezone.
	 */
	public function get_created_date() {

		return $this->created_date;
	}

	/**
	 * Set the created date.
	 *
	 * @since 1.0.0
	 *
	 * @param DateTime $created_date The created date.
	 */
	public function set_created_date( DateTime $created_date ) {

		$this->created_date = $created_date;
	}

	/**
	 * Get the send date.
	 *
	 * @since 1.0.0
	 *
	 * @return DateTime Returns the DateTime object in UTC timezone.
	 */
	public function get_send_date() {

		return $this->send_date;
	}

	/**
	 * Set the send date.
	 *
	 * @since 1.0.0
	 *
	 * @param DateTime $send_date The send date.
	 */
	public function set_send_date( DateTime $send_date ) {

		$this->send_date = $send_date;
	}

	/**
	 * Get the integration slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_integration() {

		return $this->integration;
	}

	/**
	 * Set the integration slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $integration The integration slug.
	 */
	public function set_integration( $integration ) {

		$this->integration = $integration;
	}

	/**
	 * Get the platform type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_platform_type() {

		return $this->platform_type;
	}

	/**
	 * Set the platform type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $platform_type The platform type.
	 */
	public function set_platform_type( $platform_type ) {

		$this->platform_type = $platform_type;
	}

	/**
	 * Get the platform name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_platform_name() {

		return $this->platform_name;
	}

	/**
	 * Set the platform name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $platform_name The platform name.
	 */
	public function set_platform_name( $platform_name ) {

		$this->platform_name = $platform_name;
	}

	/**
	 * Get the platform review url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_positive_review_url() {

		return $this->positive_review_url;
	}

	/**
	 * Set the platform review url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $positive_review_url The platform review url.
	 */
	public function set_positive_review_url( $positive_review_url ) {

		$this->positive_review_url = $positive_review_url;
	}

	/**
	 * Get the rating.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_rating() {

		return (int) $this->get_meta( 'rating' );
	}

	/**
	 * Set the rating.
	 *
	 * @since 1.0.0
	 *
	 * @param int $rating The rating.
	 */
	public function set_rating( $rating ) {

		$this->set_meta( 'rating', (int) $rating );
	}

	/**
	 * Get the rate date. This is the date the user clicked on the link in the review request email.
	 *
	 * @since 1.0.0
	 *
	 * @return DateTime Returns the DateTime object in UTC timezone.
	 */
	public function get_rate_date() {

		return Date::create( $this->get_meta( 'rate_date' ) );
	}

	/**
	 * Set the rate date. This is the date the user clicked on the link in the review request email.
	 *
	 * @since 1.0.0
	 *
	 * @param DateTime $date The rate date.
	 */
	public function set_rate_date( $date ) {

		$this->set_meta( 'rate_date', Date::format_db_date( $date ) );
	}

	/**
	 * Whether the positive review link was clicked.
	 *
	 * When the user clicks on the positive review link on the review reply page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_positive_review_link_clicked() {

		return $this->get_meta( 'positive_review_link_clicked', false );
	}

	/**
	 * Set whether the positive review link was clicked.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $clicked Whether the positive review link was clicked.
	 */
	public function set_positive_review_link_clicked( $clicked ) {

		$this->set_meta( 'positive_review_link_clicked', (bool) $clicked );
	}

	/**
	 * Whether the negative review form was submitted on review reply page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_negative_review_submitted() {

		return $this->get_meta( 'negative_review_submitted', false );
	}

	/**
	 * Set whether the negative review form was submitted on review reply page.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $submitted Whether the negative review form was submitted on review reply page.
	 */
	public function set_negative_review_submitted( $submitted ) {

		$this->set_meta( 'negative_review_submitted', (bool) $submitted );
	}

	/**
	 * Get the customer full name if available.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_full_name() {

		$name = '';

		if ( ! empty( $this->get_meta( 'first_name' ) ) ) {
			$name .= $this->get_meta( 'first_name' );
		}

		if ( ! empty( $this->get_meta( 'last_name' ) ) ) {
			$name .= ' ' . $this->get_meta( 'last_name' );
		}

		return trim( $name );
	}

	/**
	 * Get metadata by key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Meta key.
	 * @param mixed  $default Default value to return if the meta key is not set.
	 *
	 * @return array|mixed|null
	 */
	public function get_meta( $key = false, $default = null ) {

		if ( is_null( $this->meta ) ) {
			$this->meta = $this->data_store->get_meta( $this );
		}

		if ( $key === false ) {
			return $this->meta;
		}

		return $this->meta[ $key ] ?? $default;
	}

	/**
	 * Set metadata by key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Meta key.
	 * @param mixed  $value Meta value.
	 */
	public function set_meta( $key, $value ) {

		if ( is_null( $this->meta ) ) {
			$this->meta = $this->data_store->get_meta( $this );
		}

		$this->meta[ $key ] = $value;

		$this->meta_changed = true;
	}

	/**
	 * Whether the metadata has changed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_meta_changed() {

		return $this->meta_changed;
	}

	/**
	 * Save a new or modified review request in DB.
	 *
	 * @since 1.0.0
	 */
	public function save() {

		if ( ! empty( $this->get_id() ) ) {
			$this->data_store->update( $this );
		} else {
			$this->data_store->create( $this );
		}
	}

	/**
	 * Delete review request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function delete() {

		return $this->data_store->delete( $this );
	}

	/**
	 * Send the review request email and update status.
	 *
	 * @since 1.0.0
	 */
	public function send() {

		$is_sent = ( new ReviewRequestEmail( $this ) )->send();

		$this->set_status( $is_sent ? self::STATUS_SENT : self::STATUS_FAILED );
		$this->save();
	}
}
