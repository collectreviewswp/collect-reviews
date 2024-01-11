<?php

namespace CollectReviews\ServiceProviders;

use CollectReviews\Container;

/**
 * Abstract service provider.
 *
 * @sice 1.0.0
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface {

	/**
	 * Container instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Bootable services.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $bootable_services = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {

		$this->container = $container;
	}

	/**
	 * Get bootable services.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_bootable_services() {

		return $this->bootable_services;
	}

	/**
	 * Boot services.
	 *
	 * @since 1.0.0
	 */
	public function boot() {

		foreach ( $this->get_bootable_services() as $service ) {
			$object = $this->container->get( $service );

			if ( method_exists( $object, 'init' ) ) {
				$object->init();
			}

			if ( method_exists( $object, 'hooks' ) ) {
				$object->hooks();
			}
		}
	}
}
