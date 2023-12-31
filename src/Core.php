<?php

namespace CollectReviews;

use CollectReviews\Admin\Admin;
use CollectReviews\Admin\Scripts;
use CollectReviews\Admin\Pages\ReviewRequests as ReviewRequestsAdminPage;
use CollectReviews\Admin\Pages\Settings as SettingsPage;
use CollectReviews\Ajax\AjaxManager;
use CollectReviews\DatabaseMigrations\DatabaseMigrations;
use CollectReviews\Integrations\Integrations;
use CollectReviews\Platforms\Platforms;
use CollectReviews\ReviewRequests\Queue;
use CollectReviews\ReviewReplies\ReviewReplyPage;
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
	 * Core constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->plugin_url  = rtrim( plugin_dir_url( __DIR__ ), '/\\' );
		$this->plugin_path = rtrim( plugin_dir_path( __DIR__ ), '/\\' );

		$this->container = new Container();

		// Load all the plugin.
		$this->hooks();
		$this->init_early();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		// Activation hook.
		register_activation_hook( COLLECT_REVIEWS_PLUGIN_FILE, [ $this, 'activate' ] );

		// Initialize plugin.
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Plugin initialization.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->boot();
	}

	/**
	 * Functionality that should be initialized before `plugins_loaded` hook.
	 *
	 * @since 1.0.0
	 */
	private function init_early() {

		// Define database tables.
		$db_migrations = new DatabaseMigrations();
		$db_migrations->define_tables();
	}

	/**
	 * Register plugin modules.
	 *
	 * @since 1.0.0
	 */
	private function boot() {

		$this->container->add( Request::class, 'request' );
		$this->container->add( Options::class, 'options' );
		$this->container->add( TemplateLoader::class, 'templates' );

		$this->container->add_module( DatabaseMigrations::class, 'db-migrations' );

		$this->container->add_module( AjaxManager::class, 'ajax' );

		$this->container->add_module( Admin::class, 'admin' );
		$this->container->add_module( Scripts::class, 'scripts' );
		$this->container->add_module( ReviewRequestsAdminPage::class );
		$this->container->add_module( SettingsPage::class );

		$this->container->add_module( Queue::class );

		$this->container->add_module( ReviewReplyPage::class );

		$this->container->add_module( ReviewReplyPage::class );

		foreach ( Integrations::INTEGRATIONS as $integration_slug => $integration_class ) {
			$this->container->add_module( $integration_class, $integration_slug . '_integration' );
		}

		$this->container->add( Integrations::class, 'integrations' );

		$this->container->add( Platforms::class, 'platforms' );
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
