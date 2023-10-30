jQuery( document ).ready( function( $ ) {

	var app = {
		init: function() {

			$( '.collect-reviews-review__link' ).on( 'click', function( e ) {
				e.preventDefault();

				const $reviewLink = $( this );

				$reviewLink.addClass( 'collect-reviews-button--loading' );

				$.ajax( {
					type: 'POST',
					url: collect_reviews.ajax_url,
					data: {
						action: 'collect_reviews_ajax',
						_wpnonce: collect_reviews.ajax_nonce,
						task: 'positive_review_link_click',
						data: {
							review_request_id: $reviewLink.data( 'review-request-id' ),
							review_request_key: $reviewLink.data( 'review-request-key' ),
						},
					},
					complete: function() {
						window.location = $reviewLink.attr( 'href' );
					}
				} );
			} );

			$( '.collect-reviews-feedback__form' ).on( 'submit', function( e ) {
				e.preventDefault();

				const $submitBtn = $( this ).find( 'button[type="submit"]' ),
					$error = $( '.collect-reviews-feedback__error' );

				$submitBtn.addClass( 'collect-reviews-button--loading' );
				$error.html( '' );

				$.ajax( {
					type: 'POST',
					url: collect_reviews.ajax_url,
					data: {
						action: 'collect_reviews_ajax',
						_wpnonce: collect_reviews.ajax_nonce,
						task: 'review_form_submit',
						data: $( this ).serialize(),
					},
					success: function( response ) {
						if ( response.success ) {
							$( '.collect-reviews-feedback__form' ).hide();
							$( '.collect-reviews-feedback__success' ).show();
						} else {
							$error.html( response.data );
						}
					},
					error: function( response ) {
						$error.html( response.responseText );
						console.log( response );
					},
					complete: function() {
						$submitBtn.removeClass( 'collect-reviews-button--loading' );
					}
				} );
			} );
		},
	}

	app.init();
} );
