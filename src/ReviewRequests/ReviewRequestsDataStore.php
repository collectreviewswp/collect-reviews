<?php

namespace CollectReviews\ReviewRequests;

use CollectReviews\Helpers\Date;
use CollectReviews\ReviewRequests\Migrations\ReviewRequestsMetaTable;
use CollectReviews\ReviewRequests\Migrations\ReviewRequestsTable;
use WP_Date_Query;

/**
 * Class ReviewRequestsDataStore.
 *
 * All CRUD operations for Review Requests.
 *
 * @since 1.0.0
 */
class ReviewRequestsDataStore implements ReviewRequestsDataStoreInterface {

	/**
	 * Create a new Review Request.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review Request object.
	 *
	 * @return bool
	 */
	public function create( $review_request ) {

		global $wpdb;

		$table = ReviewRequestsTable::get_table_name();

		if ( empty( $review_request->get_key() ) ) {
			$review_request->set_key( wp_generate_password( 15, false ) );
		}

		// Create a new DB table record.
		$wpdb->insert(
			$table,
			[
				'unique_key'          => $review_request->get_key(),
				'status'              => $review_request->get_status(),
				'email'               => $review_request->get_email(),
				'created_date'        => Date::format_db_date( $review_request->get_created_date() ),
				'send_date'           => Date::format_db_date( $review_request->get_send_date() ),
				'integration'         => $review_request->get_integration(),
				'platform_type'       => $review_request->get_platform_type(),
				'platform_name'       => $review_request->get_platform_name(),
				'positive_review_url' => $review_request->get_positive_review_url(),
			],
			[
				'%s', // unique_key.
				'%d', // status.
				'%s', // email.
				'%s', // created_date.
				'%s', // send_date.
				'%s', // integration.
				'%s', // platform_type.
				'%s', // platform_name.
				'%s', // positive_review_url.
			]
		);

		$id = $wpdb->insert_id;

		$review_request->set_id( $id );

		$this->update_meta( $review_request );

		return true;
	}

	/**
	 * Retrieve a Review Request from DB.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review Request object.
	 *
	 * @return bool
	 */
	public function read( $review_request ) {

		if ( empty( $review_request->get_id() ) ) {
			return false;
		}

		global $wpdb;

		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->collect_reviews_review_requests WHERE id = %d", $review_request->get_id() ) );

		if ( empty( $result ) ) {
			$review_request->set_id( 0 );
			return false;
		}

		$this->populate_object( $review_request, $result );

		return true;
	}

	/**
	 * Update a Review Request.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review Request object.
	 *
	 * @return bool
	 */
	public function update( $review_request ) {

		if ( empty( $review_request->get_id() ) ) {
			return false;
		}

		global $wpdb;

		$table = ReviewRequestsTable::get_table_name();

		// Update the existing DB table record.
		$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			$table,
			[
				'status'              => $review_request->get_status(),
				'email'               => $review_request->get_email(),
				'created_date'        => Date::format_db_date( $review_request->get_created_date() ),
				'send_date'           => Date::format_db_date( $review_request->get_send_date() ),
				'integration'         => $review_request->get_integration(),
				'platform_type'       => $review_request->get_platform_type(),
				'platform_name'       => $review_request->get_platform_name(),
				'positive_review_url' => $review_request->get_positive_review_url(),
			],
			[
				'id' => $review_request->get_id(),
			],
			[
				'%s', // status.
				'%s', // email.
				'%s', // created_date.
				'%s', // send_date.
				'%s', // integration.
				'%s', // platform_type.
				'%s', // platform_name.
				'%s', // positive_review_url.
			],
			[
				'%d',
			]
		);

		$this->update_meta( $review_request );

		return true;
	}

	/**
	 * Delete a Review Request.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review Request object.
	 *
	 * @return bool
	 */
	public function delete( $review_request ) {

		if ( empty( $review_request->get_id() ) ) {
			return false;
		}

		global $wpdb;

		return (bool) $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->collect_reviews_review_requests WHERE id = %d", $review_request->get_id() ) );
	}

	/**
	 * Get review request metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review Request object.
	 *
	 * @return array
	 */
	public function get_meta( $review_request ) {

		if ( empty( $review_request->get_id() ) ) {
			return [];
		}

		global $wpdb;

		$result = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->collect_reviews_review_requests_meta WHERE review_request_id = %d", $review_request->get_id() ) );

		$meta = [];

		foreach ( $result as $row ) {
			$meta[ $row->meta_key ] = $row->meta_value;
		}

		return $meta;
	}

	/**
	 * Update review request metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review Request object.
	 */
	public function update_meta( $review_request ) {

		if ( empty( $review_request->get_id() ) || ! $review_request->is_meta_changed() ) {
			return;
		}

		global $wpdb;

		$data_query  = [];
		$data_values = [];

		foreach ( $review_request->get_meta() as $meta_key => $meta_value ) {
			$data_query[]  = '(%d, %s, %s)';
			$data_values[] = $review_request->get_id();
			$data_values[] = $meta_key;
			$data_values[] = $meta_value;
		}

		$data_query = implode( ',', $data_query );

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->collect_reviews_review_requests_meta} (review_request_id, meta_key, meta_value)
				VALUES {$data_query} ON DUPLICATE KEY UPDATE review_request_id=values(review_request_id),meta_key=values(meta_key),meta_value=values(meta_value)",
				$data_values
			)
		);
	}

	/**
	 * Get review requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return ReviewRequest[]|array
	 */
	public function query( $args ) {

		global $wpdb;

		$where = $this->build_where( $args );

		$order = '';

		if ( ! empty( $args['order_by'] ) && ! empty( $args['order'] ) ) {
			$order = 'ORDER BY ' . esc_sql( $args['order_by'] ) . ' ' . esc_sql( $args['order'] );
		}

		$offset   = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;
		$per_page = isset( $args['per_page'] ) ? intval( $args['per_page'] ) : - 1;
		$limit    = 'LIMIT ' . $offset;

		if ( $per_page > 0 ) {
			$limit .= ', ' . $per_page;
		}

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $wpdb->collect_reviews_review_requests WHERE {$where['query']} {$order} {$limit}",
				$where['values']
			)
		);

		if ( isset( $args['return_format'] ) && $args['return_format'] === 'raw' ) {
			return ! empty( $results ) ? $results : [];
		}

		$review_requests = [];

		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$review_request = new ReviewRequest();

				$this->populate_object( $review_request, $row );

				$review_requests[] = $review_request;
			}
		}

		return $review_requests;
	}

	/**
	 * Get total number of Review Requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return int
	 */
	public function get_count( $args ) {

		global $wpdb;

		$where = $this->build_where( $args );

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $wpdb->collect_reviews_review_requests WHERE {$where['query']}",
				$where['values']
			)
		);
	}

	/**
	 * Build WHERE clause for query.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return array
	 */
	private function build_where( $args ) {

		$query  = '%d=%d';
		$values = [ 1, 1 ];

		if (
			! empty( $args['id'] ) ||
			! empty( $args['ids'] )
		) {
			if ( ! empty( $args['id'] ) ) {
				$query    .= ' AND id = %s';
				$values[] = $args['id'];
			} elseif ( ! empty( $args['ids'] ) ) {
				$query  .= ' AND id IN (' . implode( ',', array_fill( 0, count( $args['ids'] ), '%d' ) ) . ')';
				$values = array_merge( $values, $args['ids'] );
			}

			// When some ID(s) defined - we should ignore all other possible filtering options.
			return [
				'query'  => $query,
				'values' => $values,
			];
		}

		if ( ! empty( $args['email'] ) ) {
			$query    .= ' AND email = %s';
			$values[] = $args['email'];
		}

		if ( isset( $args['status'] ) ) {
			$query    .= ' AND status = %d';
			$values[] = $args['status'];
		}

		// TODO: maybe implement self basic date query (next release).
		if ( ! empty( $args['date_query'] ) ) {
			add_filter( 'date_query_valid_columns', [ $this, 'date_query_valid_columns' ] );
			$date_query = new WP_Date_Query( $args['date_query'], 'created_date' );
			$query      .= $date_query->get_sql();
			remove_filter( 'date_query_valid_columns', [ $this, 'date_query_valid_columns' ] );
		}

		return [
			'query'  => $query,
			'values' => $values,
		];
	}

	/**
	 * Date query valid columns filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	public function date_query_valid_columns( $columns ) {

		$columns[] = 'created_date';
		$columns[] = 'send_date';

		return $columns;
	}

	/**
	 * Populate review request object with data.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request Review request object.
	 * @param object        $data           Review request data.
	 */
	private function populate_object( $review_request, $data ) {

		foreach ( get_object_vars( $data ) as $key => $value ) {
			if ( $key === 'unique_key' ) {
				$key = 'key';
			}

			$setter = "set_$key";

			if ( is_callable( [ $review_request, $setter ] ) ) {
				if ( in_array( $key, [ 'created_date', 'send_date' ] ) ) {
					$value = Date::create( $value );
				}

				$review_request->{$setter}( $value );
			}
		}
	}
}
