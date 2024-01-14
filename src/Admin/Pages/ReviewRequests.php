<?php

namespace CollectReviews\Admin\Pages;

use CollectReviews\Ajax\Ajaxable;
use CollectReviews\Helpers\Date;
use CollectReviews\Platforms\Platform;
use CollectReviews\ReviewRequests\ReviewRequest;
use CollectReviews\ReviewRequests\ReviewRequestsQuery;
use DateInterval;
use WP_Error;

/**
 * Class ReviewRequests.
 *
 * @since 1.0.0
 */
class ReviewRequests {

	/**
	 * Ajaxable trait.
	 *
	 * @since 1.0.0
	 */
	use Ajaxable;

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		$this->register_ajax( 'get_review_requests' );
		$this->register_ajax( 'create_review_request' );
		$this->register_ajax( 'delete_review_request' );
	}

	/**
	 * Get review requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Input data.
	 *
	 * @return array
	 */
	public function get_review_requests( $data ) {

		$page     = isset( $data['page'] ) ? intval( $data['page'] ) : 0;
		$per_page = isset( $data['per_page'] ) ? intval( $data['per_page'] ) : ReviewRequestsQuery::DEFAULT_PER_PAGE;

		$query_args = [
			'offset'   => $page * $per_page,
			'per_page' => $per_page,
		];

		if ( isset( $data['order'] ) ) {
			$query_args['order'] = sanitize_key( $data['order'] );
		}

		if ( isset( $data['order_by'] ) ) {
			$query_args['order_by'] = sanitize_key( $data['order_by'] );
		}

		$query = new ReviewRequestsQuery( $query_args );

		$review_requests = array_map( function ( ReviewRequest $request ) {

			$reply_status = 0;

			if ( ! empty( $request->get_rating() ) ) {
				$reply_status = 1;
			}

			if ( $request->is_negative_review_submitted() ) {
				$reply_status = 2;
			}

			if ( $request->is_positive_review_link_clicked() ) {
				$reply_status = 3;
			}

			$refs = [];

			// TODO: move to separate class per integration (next release).
			if ( $request->get_integration() === 'woocommerce' ) {
				$order_id = $request->get_meta( 'order_id' );

				if ( ! empty( $order_id ) ) {
					$order_ref = [
						'text' => sprintf( /* translators: %1$s - order id. */
							esc_html__( 'Order #%1$s', 'collect-reviews' ),
							intval( $order_id )
						),
					];

					if ( function_exists( 'WC' ) ) {
						$order = wc_get_order( $order_id );

						if ( ! empty( $order ) ) {
							$order_ref['text'] = sprintf( /* translators: %1$s - order id. */
								esc_html__( 'Order #%1$s', 'collect-reviews' ),
								esc_html( $order->get_order_number() )
							);
							$order_ref['url']  = admin_url( 'post.php?post=' . $order_id . '&action=edit' );
						}
					}

					$refs[] = $order_ref;
				}
			} elseif ( $request->get_integration() === 'easy_digital_downloads' ) {
				$payment_id = $request->get_meta( 'payment_id' );

				if ( ! empty( $payment_id ) ) {
					$order_ref = [
						'text' => sprintf( /* translators: %1$s - order id. */
							esc_html__( 'Order #%1$s', 'collect-reviews' ),
							intval( $payment_id )
						),
					];

					if ( function_exists( 'EDD' ) ) {
						$order_ref['url'] = edd_get_admin_url( array(
							'page' => 'edd-payment-history',
							'view' => 'view-order-details',
							'id'   => absint( $payment_id ),
						) );
					}

					$refs[] = $order_ref;
				}
			} elseif ( $request->get_integration() === 'wpforms' ) {
				$form_id = $request->get_meta( 'form_id' );

				if ( ! empty( $form_id ) ) {
					$form_ref = [
						'text' => sprintf( /* translators: %1$s - form id. */
							esc_html__( 'Form #%1$s', 'collect-reviews' ),
							intval( $form_id )
						),
					];

					if ( function_exists( 'wpforms' ) ) {
						$form = wpforms()->get( 'form' )->get( $form_id );

						if ( ! empty( $form ) ) {
							$form_ref['tooltip'] = $form->post_title;
						}
					}

					$refs[] = $form_ref;
				}

				$entry_id = $request->get_meta( 'entry_id' );

				if ( ! empty( $entry_id ) ) {
					$entry_ref = [
						'text' => sprintf( /* translators: %1$s - entry id. */
							esc_html__( 'Entry #%1$s', 'collect-reviews' ),
							intval( $entry_id )
						),
						'url'  => esc_url( admin_url( 'admin.php?page=wpforms-entries&view=details&entry_id=' . intval( $entry_id ) ) ),
					];

					$refs[] = $entry_ref;
				}
			}

			return [
				'id'            => $request->get_id(),
				'status'        => $request->get_status(),
				'email'         => $request->get_email(),
				'platform_name' => $request->get_platform_name(),
				'created_date'  => Date::format_display_date( $request->get_created_date() ),
				'send_date'     => Date::format_display_date( $request->get_send_date() ),
				'rate_date'     => Date::format_display_date( $request->get_rate_date() ),
				'rating'        => $request->get_rating(),
				'reply_status'  => $reply_status,
				'refs'          => $refs
			];
		}, $query->query() );

		return [
			'total_count' => $query->get_count(),
			'list'        => $review_requests
		];
	}

	/**
	 * Create review request.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Input data.
	 *
	 * @return true|WP_Error
	 */
	public function create_review_request( $data ) {

		if ( ! isset( $data['email'], $data['delay'], $data['platform_type'], $data['platform_name'], $data['platform_review_url'] ) ) {
			return new WP_Error( 'missed_required_params', esc_html__( 'Missed required parameters.', 'collect-reviews' ) );
		}

		$email               = sanitize_email( $data['email'] );
		$request_delay       = intval( $data['delay'] );
		$platform_type       = sanitize_text_field( $data['platform_type'] );
		$platform_name       = sanitize_text_field( $data['platform_name'] );
		$platform_review_url = esc_url_raw( $data['platform_review_url'] );

		$first_name = isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '';
		$last_name  = isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '';

		$platform = new Platform( [
			'type'       => $platform_type,
			'name'       => $platform_name,
			'review_url' => $platform_review_url,
		] );

		$created_date        = Date::create( 'now' );
		$request_review_date = clone $created_date;

		if ( $request_delay > 0 ) {
			$request_review_date->add( new DateInterval( "PT{$request_delay}S" ) );
			$request_review_date->setTime( $request_review_date->format( 'H' ), 0 );
		}

		$review_request = new ReviewRequest();

		$review_request->set_status( ReviewRequest::STATUS_PENDING );
		$review_request->set_email( $email );
		$review_request->set_created_date( $created_date );
		$review_request->set_send_date( $request_review_date );
		$review_request->set_platform_type( $platform->get_type() );
		$review_request->set_platform_name( $platform->get_name() );
		$review_request->set_positive_review_url( $platform->get_review_url() );
		$review_request->set_integration( 'manual' );

		if ( ! empty( $first_name ) ) {
			$review_request->set_meta( 'first_name', $first_name );
		}

		if ( ! empty( $last_name ) ) {
			$review_request->set_meta( 'last_name', $last_name );
		}

		$review_request->save();

		if ( $request_delay === 0 ) {
			$review_request->send();
		}

		return true;
	}

	/**
	 * Delete review request.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Input data.
	 *
	 * @return true|WP_Error
	 */
	public function delete_review_request( $data ) {

		if ( ! isset( $data['id'] ) ) {
			return new WP_Error( 'missed_required_params', esc_html__( 'Missed required parameters.', 'collect-reviews' ) );
		}

		$review_request_id = intval( $data['id'] );
		$review_request    = new ReviewRequest( $review_request_id );

		if ( $review_request->get_id() === 0 ) {
			return new WP_Error( 'not_found', esc_html__( 'Review request not found.', 'collect-reviews' ) );
		}

		return $review_request->delete();
	}
}
