<?php

namespace CollectReviews;

use CollectReviews\Vendor\League\Container\Container as VendorContainer;
use CollectReviews\Vendor\League\Container\Definition\DefinitionInterface;
use CollectReviews\Vendor\Psr\Container\ContainerExceptionInterface;
use CollectReviews\Vendor\Psr\Container\NotFoundExceptionInterface;

/**
 * Class Container.
 *
 * PSR11 compliant dependency injection container.
 *
 * @since 1.0.0
 */
final class Container {

	/**
	 * The underlying container.
	 *
	 * @since 1.0.0
	 *
	 * @var VendorContainer
	 */
	private $container;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->container = new VendorContainer();
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 *
	 * @return mixed Entry.
	 */
	public function get( $id ) {

		return $this->container->get( $id );
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return bool
	 */
	public function has( $id ) {

		return $this->container->has( $id );
	}

	/**
	 * Adds a new definition to the container.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class Class name.
	 *
	 * @return DefinitionInterface
	 */
	public function add( $class ) {

		return $this->container->addShared( $class );
	}
}
