<?php

namespace CollectReviews\ReviewRequests\Email\SmartTags;

use CollectReviews\Emails\SmartTags\SmartTagInterface;
use CollectReviews\ReviewRequests\ReviewRequest;

/**
 * Class RatingStars. Represents the rating stars block.
 *
 * @since 1.0.0
 */
class RatingStars implements SmartTagInterface {

	/**
	 * Review request.
	 *
	 * @since 1.0.0
	 *
	 * @var ReviewRequest
	 */
	private $review_request;

	/**
   * Rating stars style.
   *
	 * @since 1.1.0
	 *
	 * @var string
	 */
  private $rating_stars_style;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ReviewRequest $review_request     Review request.
	 * @param string|false  $rating_stars_style Rating stars style.
	 */
	public function __construct( $review_request, $rating_stars_style = false ) {

		$this->review_request = $review_request;

		if ( $rating_stars_style === false ) {
			$this->rating_stars_style = collect_reviews()->get( 'options' )->get( 'review_request_email.rating_stars_style', 'gradient' );
		} else {
			$this->rating_stars_style = $rating_stars_style;
		}
	}

	/**
	 * Get the smart tag value.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_value() {

		if ( $this->rating_stars_style === 'classic' ) {
			$rating_colors = [
				5 => '#FFD700',
				4 => '#FFD700',
				3 => '#FFD700',
				2 => '#FFD700',
				1 => '#FFD700',
			];
		} else {
			$rating_colors = [
				5 => '#57e32c',
				4 => '#b7dd29',
				3 => '#ffe234',
				2 => '#ffa534',
				1 => '#ff4545',
			];
		}

		$base_url = add_query_arg(
			[
				'collect_reviews_review_replay' => 1,
				'review_request_id'             => $this->review_request->get_id(),
				'review_request_key'            => $this->review_request->get_key()
			],
			home_url()
		);

		ob_start();
		?>
		<table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation">
			<tbody>
			<tr>
				<td>
					<?php for ( $i = 0; $i < 5; $i ++ ) :
						$rating = 5 - $i;

						if ( $this->rating_stars_style === 'classic' ) {
							$star_height = 25;
						} else {
							$star_height = 25 - $i * 2;
						}

						$url = add_query_arg( 'rating', $rating, $base_url );
						?>
						<table align="center" width="100%" style="margin-bottom:10px;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif" border="0" cellPadding="0" cellSpacing="0" role="presentation">
							<tbody>
							<tr>
								<td style="width:26px">
									<a href="<?php echo esc_url( $url ); ?>" target="_blank" style="font-size:13px;line-height:100%;text-decoration:none;display:inline-block;max-width:100%;padding:7px 10px 7px 0px">
										<span><!--[if mso]><i style="letter-spacing: 7px 0px;mso-font-width:-100%;mso-text-raise:null" hidden>&nbsp;</i><![endif]--></span>
										<span style="max-width:100%;display:inline-block;line-height:120%;mso-padding-alt:0px"><span style="display:inline-block;padding:7px;border-radius:50%;border:1px solid #BDBDC3;font-size:0;line-height:0;vertical-align:middle"></span>
										</span><span><!--[if mso]><i style="letter-spacing: 7px 0px;mso-font-width:-100%" hidden>&nbsp;</i><![endif]--></span>
									</a>
								</td>
								<td>
									<a href="<?php echo esc_url( $url ); ?>" target="_blank" style="color:#57e32c;border-radius:20px;border:1px solid #BDBDC3;font-size:20px;line-height:100%;text-decoration:none;display:inline-block;max-width:100%;padding:5px 10px">
										<span><!--[if mso]><i style="letter-spacing: 10px;mso-font-width:-100%;mso-text-raise:7.5" hidden>&nbsp;</i><![endif]--></span>
										<span style="max-width:100%;display:inline-block;line-height:120%;mso-padding-alt:0px;mso-text-raise:3.75px">
											<?php
											$starts = '';

											for ( $n = 0; $n < 5; $n ++ ) {
												$color = $rating <= $n ? '#BDBDC3' : $rating_colors[ $rating ];

												$starts .= "<span style=\"font-size: {$star_height}px; line-height: {$star_height}px; color: {$color};\">★</span>";

												if ( $n < 4 ) {
													$starts .= '&nbsp;';
												}
											}

											echo wp_kses_post( $starts );
											?>
										</span>
										<span><!--[if mso]><i style="letter-spacing: 10px;mso-font-width:-100%" hidden>&nbsp;</i><![endif]--></span>
									</a>
								</td>
							</tr>
							</tbody>
						</table>
					<?php endfor; ?>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
		return ob_get_clean();
	}
}
