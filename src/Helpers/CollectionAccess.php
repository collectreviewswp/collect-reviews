<?php

namespace CollectReviews\Helpers;

/**
 * Class CollectionAccess. Provides access to nested array values by string key in OOP manner.
 *
 * @since 1.0.0
 */
class CollectionAccess {

	/**
	 * Original array.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $array;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array Original array.
	 */
	public function __construct( $array ) {

		$this->array = $array;
	}

	/**
	 * Get nested array value by string key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $keys    String key. E.g. "level1.level2.level3".
	 * @param mixed  $default The default value that should be returned if value by key not found.
	 *
	 * @return mixed
	 */
	public function get( $keys, $default = null ) {

		return Collection::get( $this->array, $keys, $default );
	}

	/**
	 * Get the original array.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_all() {

		return $this->array;
	}
}
