<?php

namespace CollectReviews\DataStore;

/**
 * Interface ObjectMetaDataStoreInterface.
 *
 * @since 1.0.0
 */
interface ObjectMetaDataStoreInterface {

	/**
	 * Get meta data for object.
	 *
	 * @since 1.0.0
	 *
	 * @param $object
	 *
	 * @return mixed
	 */
	public function get_meta( $object );

	/**
	 * Update meta data for object.
	 *
	 * @since 1.0.0
	 *
	 * @param $object
	 *
	 * @return bool
	 */
	public function update_meta( $object );
}
