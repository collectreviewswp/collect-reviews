<?php

namespace CollectReviews\ReviewRequests\Email;

use CollectReviews\Emails\TemplateInterface;
use CollectReviews\Helpers\Uploads;

/**
 * Class Template. Review request email template.
 *
 * @since 1.0.0
 */
class Template implements TemplateInterface {

	/**
	 * Email content.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $content;

	/**
	 * Email arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $args;

	/**
	 * Email logo.
	 *
	 * @since 1.0.0
	 *
	 * @var Logo
	 */
	private $logo = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Email content.
	 * @param array  $args    Email arguments.
	 */
	public function __construct( $content, $args = [] ) {

		$this->content = $content;

		$this->args = wp_parse_args( $args, [
			'logo'        => [],
			'footer_text' => '',
		] );
	}

	/**
	 * Get email HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get() {

		// TODO: implement dark morde and maybe inject inline styles (next release).
		$content = $this->get_header();
		$content .= $this->content;
		$content .= $this->get_footer();

		return $content;
	}

	/**
	 * Get email header HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_header() {

		ob_start();
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="en">

		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		</head>

		<body style="background-color:#f1f1f1;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif;font-size:16px;margin:0;">
		<table class="body" align="center" width="100%"  style="background-color:#f1f1f1;font-size:16px;padding:30px 0" border="0" cellPadding="0" cellSpacing="0" role="presentation">
		<tbody>
		<tr>
		<td>
		<table align="center" width="100%" role="presentation" cellSpacing="0" cellPadding="0" border="0" style="max-width:37.5em;margin-bottom:30px">
			<tbody>
			<tr style="width:100%">
				<td>
					<?php $this->get_logo()->display(); ?>
				</td>
			</tr>
			</tbody>
		</table>
		<table align="center" width="100%"  role="presentation" cellSpacing="0" cellPadding="0" border="0" style="max-width:37.5em;border-radius:6px;background-color:#ffffff;padding:34px 50px">
		<tbody>
		<tr style="width:100%">
		<td>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get email footer HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_footer() {

		ob_start();
		?>
		</td>
		</tr>
		</tbody>
		</table>
		<table align="center" width="100%" role="presentation" cellSpacing="0" cellPadding="0" border="0" style="max-width:37.5em;margin-top:30px">
			<tbody>
			<tr style="width:100%">
				<td>
					<p style="font-size:13px;line-height:24px;margin:0;text-align:center;color:#777777"><?php echo wp_kses_post( $this->args['footer_text'] ); ?></p>
				</td>
			</tr>
			</tbody>
		</table>
		</td>
		</tr>
		</tbody>
		</table>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get email logo.
	 *
	 * @since 1.0.0
	 *
	 * @return Logo
	 */
	private function get_logo() {

		if ( ! is_null( $this->logo ) ) {
			return $this->logo;
		}

		$logo = new Logo();

		if ( ! empty( $this->args['logo']['url'] ) ) {
			$logo->set_url( $this->args['logo']['url'] );
		} else if (
			! empty( $this->args['logo']['filename'] ) &&
			Uploads::file_exists( $this->args['logo']['filename'] )
		) {
			$logo->set_url( Uploads::get_file_url( $this->args['logo']['filename'] ) );
		}

		if ( ! empty( $this->args['logo']['width'] ) && ! empty( $this->args['logo']['height'] ) ) {
			$width        = $this->args['logo']['width'];
			$height       = $this->args['logo']['height'];
			$aspect_ratio = $width / $height;
			$max_width    = 300;
			$max_height   = 100;

			if ( $width > $max_width ) {
				$width  = $max_width;
				$height = $max_width / $aspect_ratio;
			}

			if ( $height > $max_height ) {
				$width  = $max_height * $aspect_ratio;
				$height = $max_height;
			}

			$logo->set_width( $width );
			$logo->set_height( $height );
		}

		$this->logo = $logo;

		return $this->logo;
	}
}
