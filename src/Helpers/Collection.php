<?php

namespace CollectReviews\Helpers;

use ArrayAccess;

/**
 * Class Collection.
 *
 * Helper class that allows to get/set value in the nested array by string key.
 *
 * @since 1.0.0
 */
class Collection {

	/**
	 * Keys string separator.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const KEY_SEPARATOR = '.';

	/**
	 * Get nested array value by string key.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $array   Input array.
	 * @param string $str_key String key. E.g. "level1.level2.level3".
	 * @param mixed  $default The default value that should be returned if value by key not found.
	 *
	 * @return mixed
	 */
	public static function get( $array, $str_key, $default = null ) {

		if ( is_array( $array ) && array_key_exists( $str_key, $array ) ) {
			return $array[ $str_key ];
		}

		$keys = self::get_keys_array( $str_key );

		foreach ( $keys as $key ) {
			if ( ! is_array( $array ) && ! $array instanceof ArrayAccess ) {
				return $default;
			}

			if (
				( $array instanceof ArrayAccess && $array->offsetExists( $key ) ) ||
				array_key_exists( $key, $array )
			) {
				$array = $array[ $key ];
			} else {
				return $default;
			}
		}

		return $array;
	}

	/**
	 * Set value in the nested array by string key.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $array   Input array.
	 * @param string $str_key String key. E.g. "level1.level2.level3".
	 * @param mixed  $value   Value that should be added to array.
	 *
	 * @return array
	 */
	public static function set( $array, $str_key, $value ) {

		$result = $array;
		$keys   = self::get_keys_array( $str_key );

		$tmp = &$result;

		$keys_count = count( $keys );

		while ( $keys_count > 0 ) {
			$key = array_shift( $keys );

			$keys_count --;

			if ( ! is_array( $tmp ) ) {
				$tmp = [];
			}

			$tmp = &$tmp[ $key ];
		}

		$tmp = $value;

		return $result;
	}

	/**
	 * Get keys array from keys string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $keys String key. E.g. "level1.level2.level3".
	 *
	 * @return array
	 */
	private static function get_keys_array( $keys ) {

		return explode( self::KEY_SEPARATOR, $keys );
	}

	/**
	 * Find a value in an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key         The key to search for.
	 * @param string $value       The value to search for.
	 * @param bool   $desired_key The key to return.
	 * @param string $default     The default value to return.
	 *
	 * @return mixed
	 */
	public static function find( $array, $key, $value, $desired_key = false, $default = '' ) {

		foreach ( $array as $item ) {
			if ( isset( $item[ $key ] ) && $item[ $key ] === $value ) {
				if ( $desired_key !== false ) {
					if ( isset( $item[ $desired_key ] ) ) {
						return $item[ $desired_key ];
					}
					break;
				}

				return $item;
			}
		}

		return $default;
	}

	/**
	 * Walk through array recursively.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $array      Array to walk through.
	 * @param callable $callback   Callback function that will be executed for each scalar array item.
	 * @param string   $parent_key Parent key. Used for recursive calls.
	 */
	public static function walk_recursive( &$array, $callback, $parent_key = '' ) {

		foreach ( $array as $key => &$value ) {
			if ( ! is_numeric( $key ) ) {
				$current_key = $parent_key ? $parent_key . '.' . $key : $key;
			} else {
				$current_key = $parent_key;
			}

			if ( is_array( $value ) ) {
				self::walk_recursive( $value, $callback, $current_key );
			} else {
				call_user_func_array( $callback, array( &$value, $current_key ) );
			}
		}
	}
}
