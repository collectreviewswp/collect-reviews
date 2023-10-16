<?php

namespace CollectReviews\Integrations;

use CollectReviews\ModuleInterface;

/**
 * Class AbstractIntegration. Base class for integration.
 *
 * Holds integration options and provides integration's methods.
 *
 * @since 1.0.0
 */
abstract class AbstractIntegration implements ModuleInterface {

	/**
	 * Integration options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Integration handler.
	 *
	 * @since 1.0.0
	 *
	 * @var AbstractHandler
	 */
	protected $handler;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->options = collect_reviews()->get( 'options' )->get( 'integrations.' . static::get_slug() );
	}

	/**
	 * Get the slug of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static abstract function get_slug();

	/**
	 * Get the title of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public abstract function get_title();

	/**
	 * Whether integration plugin is installed and activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public abstract function is_available();

	/**
	 * Get the configured triggers of the integration.
	 *
	 * @since 1.0.0
	 *
	 * @return AbstractTrigger[]
	 */
	public abstract function get_triggers();

	/**
	 * Whether the integration is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enabled() {

		return ! empty( $this->options['enabled'] );
	}

	/**
	 * Whether the integration is configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		$triggers = $this->get_triggers();

		if ( empty( $triggers ) ) {
			return false;
		}

		// Check if at least one trigger is valid.
		foreach ( $triggers as $trigger ) {
			if ( $trigger->is_valid() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the integration options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_options() {

		return $this->options;
	}
}
