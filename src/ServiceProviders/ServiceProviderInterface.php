<?php

namespace CollectReviews\ServiceProviders;

use CollectReviews\Container;

/**
 * Service provider interface.
 *
 * @since 1.0.0
 */
interface ServiceProviderInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container );

	/**
	 * Register services.
	 *
	 * @since 1.0.0
	 */
	public function register();

	/**
	 * Bootstrap services.
	 *
	 * @since 1.0.0
	 */
	public function boot();
}
