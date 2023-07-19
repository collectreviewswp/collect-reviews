<?php

namespace CollectReviews\DataStore;

/**
 * Interface ObjectDataStoreInterface.
 *
 * @since 1.0.0
 */
interface ObjectDataStoreInterface {

	/**
	 * Create a new object.
	 *
	 * @param object $object
	 *
	 * @return bool
	 */
	public function create( $object );

	/**
	 * Read an object.
	 *
	 * @param object $object
	 *
	 * @return bool
	 */
	public function read( $object );

	/**
	 * Update an object.
	 *
	 * @param object $object
	 *
	 * @return bool
	 */
	public function update( $object );

	/**
	 * Delete an object.
	 *
	 * @param object $object
	 *
	 * @return bool
	 */
	public function delete( $object );
}
