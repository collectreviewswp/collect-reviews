<?php

namespace CollectReviews;

use CollectReviews\ServiceProviders\IntegrationsServiceProvider;
use CollectReviews\ServiceProviders\MainServiceProvider;
use CollectReviews\ServiceProviders\ServiceProviderInterface;
use Exception;

/**
 * Class Core.
 *
 * Handle all plugin initialization.
 *
 * @since 1.0.0
 */
class Core {

	/**
	 * Service providers to register.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	private static $service_providers = [
		MainServiceProvider::class,
		IntegrationsServiceProvider::class
	];

	/**
	 * URL to plugin directory.
	 *
	 * @since 1.0.0
	 *
	 * @var string Without trailing slash.
	 */
	private $plugin_url;

	/**
	 * Path to plugin directory.
	 *
	 * @since 1.0.0
	 *
	 * @var string Without trailing slash.
	 */
	private $plugin_path;

	/**
	 * Plugin container.
	 *
	 * @since 1.0.0
	 *
	 * @var Container
	 */
	private $container;

	/**
	 * Registered service providers.
	 *
	 * @since 1.0.0
	 *
	 * @var ServiceProviderInterface[]
	 */
	private $registered_service_providers;

	/**
	 * Core constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->plugin_url  = rtrim( plugin_dir_url( __DIR__ ), '/\\' );
		$this->plugin_path = rtrim( plugin_dir_path( __DIR__ ), '/\\' );

		$this->container = new Container();

		// Register service providers.
		foreach ( self::$service_providers as $service_provider_class ) {
			$this->register_service_provider( new $service_provider_class( $this->container ) );
		}

		// Bootstrap the plugin.
		add_action( 'plugins_loaded', [ $this, 'bootstrap' ] );

		// Register activation hook.
		register_activation_hook( COLLECT_REVIEWS_PLUGIN_FILE, [ $this, 'activate' ] );
	}

	/**
	 * Register a service provider.
	 *
	 * @since 1.0.0
	 *
	 * @param ServiceProviderInterface $service_provider Service provider.
	 */
	private function register_service_provider( $service_provider ) {

		$service_provider->register();

		$this->registered_service_providers[] = $service_provider;
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @since 1.0.0
	 */
	public function bootstrap() {

		// Bootstrap service providers.
		foreach ( $this->registered_service_providers as $service_provider ) {
			$service_provider->boot();
		}
	}

	/**
	 * Plugin activation hook.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Store the plugin version when the initial installation occurred.
		add_option( 'collect_reviews_initial_version', COLLECT_REVIEWS_PLUGIN_VER, '', false );

		// Store the timestamp of first plugin activation.
		add_option( 'collect_reviews_initial_activation_time', time(), '', false );

		// Store the plugin version activated to reference with upgrades.
		update_option( 'collect_reviews_version', COLLECT_REVIEWS_PLUGIN_VER, false );

		// Set default options.
		$options = new Options();

		if ( empty( $options->get_all() ) ) {
			$options->update( $options->get_default() );
		}
	}

	/**
	 * Get a class instance from a container.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class Class name or an alias.
	 *
	 * @return mixed|null
	 */
	public function get( $class ) {

		try {
			return $this->container->get( $class );
		} catch ( Exception $e ) {
			return null;
		}
	}

	/**
	 * Get URL to plugin directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_url() {

		return $this->plugin_url;
	}

	/**
	 * Get Path to plugin directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_path() {

		return $this->plugin_path;
	}
}
