<?php

namespace CollectReviews\ReviewRequests;

/**
 * Class ReviewRequestsQuery.
 *
 * Query review requests.
 *
 * @since 1.0.0
 */
class ReviewRequestsQuery {

	/**
	 * Default number of items per page.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	const DEFAULT_PER_PAGE = 30;

	/**
	 * Query arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $args;

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
	 * @param array $args Query arguments.
	 */
	public function __construct( $args ) {

		$this->args       = $this->parse_args( $args );
		$this->data_store = new ReviewRequestsDataStore();
	}

	/**
	 * Get default query arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_default_query_args() {

		return [
			'offset'   => 0,
			'per_page' => self::DEFAULT_PER_PAGE,
			'order'    => 'DESC',
			'order_by' => 'created_date',
		];
	}

	/**
	 * Parse query arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private function parse_args( $args ) {

		$args        = (array) $args;
		$parsed_args = [];

		// Single ID.
		if ( ! empty( $args['id'] ) ) {
			$parsed_args['id'] = sanitize_text_field( $args['id'] );
		}

		// Multiple IDs.
		if (
			! empty( $args['ids'] ) &&
			is_array( $args['ids'] )
		) {
			$parsed_args['ids'] = array_unique(
				array_filter(
					array_map( 'sanitize_text_field', array_values( $args['ids'] ) )
				)
			);
		}

		// By Email.
		if ( ! empty( $args['email'] ) ) {
			$parsed_args['email'] = sanitize_email( $args['email'] );
		}

		// By Status.
		if ( isset( $args['status'] ) ) {
			$parsed_args['status'] = intval( $args['status'] );
		}

		// Limit.
		if ( ! empty( $args['offset'] ) ) {
			$parsed_args['offset'] = (int) $args['offset'];
		}

		if ( ! empty( $args['per_page'] ) ) {
			$parsed_args['per_page'] = (int) $args['per_page'];
		}

		// Order.
		if (
			! empty( $args['order'] ) &&
			is_string( $args['order'] ) &&
			in_array( strtoupper( $args['order'] ), [ 'ASC', 'DESC' ], true )
		) {
			$parsed_args['order'] = strtoupper( $args['order'] );
		}

		if ( ! empty( $args['order_by'] ) ) {
			$parsed_args['order_by'] = sanitize_key( $args['order_by'] );
		}

		if ( ! empty( $args['date_query'] ) ) {
			$parsed_args['date_query'] = $args['date_query'];
		}

		if ( ! empty( $args['return_format'] ) && in_array( $args['return_format'], [ 'object', 'raw' ] ) ) {
			$parsed_args['return_format'] = $args['return_format'];
		}

		// Merge missing values with defaults.
		return wp_parse_args(
			$parsed_args,
			$this->get_default_query_args()
		);
	}

	/**
	 * Get review requests.
	 *
	 * @since 1.0.0
	 *
	 * @return ReviewRequest[]|array
	 */
	public function query() {

		return $this->data_store->query( $this->args );
	}

	/**
	 * Get total number of review requests.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_count() {

		return $this->data_store->get_count( $this->args );
	}
}
