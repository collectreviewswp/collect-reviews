<?php

namespace CollectReviews\Ajax;

/**
 * Trait Ajaxable. Helps to register ajax handlers.
 *
 * @since 1.0.0
 */
trait Ajaxable {

	/**
	 * Register ajax handler.
	 *
	 * @param string $task       The task name.
	 * @param string $callback   The callback name. If empty, $task will be used as callback name.
	 * @param string $capability The capability required to perform the task.
	 */
	public function register_ajax( $task, $callback = false, $capability = 'manage_options' ) {

		if ( empty( $callback ) ) {
			$callback = $task;
		}

		collect_reviews()->get( 'ajax' )->register( $task, [ $this, $callback ], $capability );
	}
}
