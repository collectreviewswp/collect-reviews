<?php

namespace CollectReviews\Emails\SmartTags;

/**
 * Class Processor.
 *
 * @since 1.0.0
 */
class Processor {

	/**
	 * Smart tags.
	 *
	 * @since 1.0.0
	 *
	 * @var SmartTagInterface[]
	 */
	private $smart_tags;

	/**
	 * Cache.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $cache = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param SmartTagInterface[] $smart_tags Smart tags.
	 */
	public function __construct( $smart_tags ) {

		$this->smart_tags = array_filter( $smart_tags, function ( $smart_tag ) {

			return $smart_tag instanceof SmartTagInterface;
		} );
	}

	/**
	 * Process smart tags in the provided content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public function process( $content ) {

		$smart_tags_keys = array_keys( $this->smart_tags );

		$pattern = "/(" . implode( '|', array_map( 'preg_quote', $smart_tags_keys ) ) . ")/i";

		return preg_replace_callback( $pattern, [ $this, 'process_tag' ], $content );
	}

	/**
	 * Process single smart tag. The `preg_replace_callback` function callback.
	 *
	 * @since 1.0.0
	 *
	 * @param array $matches Preg replace matches.
	 *
	 * @return string
	 */
	public function process_tag( $matches ) {

		$tag = $matches[1] ?? '';

		if ( isset( $this->cache[ $tag ] ) ) {
			return $this->cache[ $tag ];
		}

		$this->cache[ $tag ] = $this->smart_tags[ $tag ] ? $this->smart_tags[ $tag ]->get_value() : '';

		return $this->cache[ $tag ];
	}
}
