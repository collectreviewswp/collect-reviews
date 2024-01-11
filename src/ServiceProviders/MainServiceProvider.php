<?php

namespace CollectReviews\ServiceProviders;

use CollectReviews\Admin\Admin;
use CollectReviews\Admin\Pages\ReviewRequests as ReviewRequestsAdminPage;
use CollectReviews\Admin\Pages\Settings as SettingsPage;
use CollectReviews\Admin\Scripts;
use CollectReviews\Ajax\AjaxManager;
use CollectReviews\DatabaseMigrations\DatabaseMigrations;
use CollectReviews\Options;
use CollectReviews\Platforms\Platforms;
use CollectReviews\Request;
use CollectReviews\ReviewReplies\ReviewReplyPage;
use CollectReviews\ReviewRequests\Queue;
use CollectReviews\TemplateLoader;

/**
 * Main service provider.
 *
 * @since 1.0.0
 */
class MainServiceProvider extends AbstractServiceProvider {

	/**
	 * Bootable services.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $bootable_services = [
		'ajax',
		'admin',
		DatabaseMigrations::class,
		Scripts::class,
		ReviewRequestsAdminPage::class,
		SettingsPage::class,
		Queue::class,
		ReviewReplyPage::class,
	];

	/**
	 * Register plugin services.
	 *
	 * @since 1.0.0
	 */
	public function register() {

		$this->container->add( Request::class )->setAlias( 'request' );
		$this->container->add( Options::class )->setAlias( 'options' );
		$this->container->add( TemplateLoader::class )->setAlias( 'templates' );
		$this->container->add( AjaxManager::class )->setAlias( 'ajax' );
		$this->container->add( Admin::class )->setAlias( 'admin' );

		$this->container->add( DatabaseMigrations::class );
		$this->container->add( Scripts::class );
		$this->container->add( ReviewRequestsAdminPage::class );
		$this->container->add( SettingsPage::class );

		$this->container->add( Queue::class );

		$this->container->add( ReviewReplyPage::class );

		$this->container->add( Platforms::class )->setAlias( 'platforms' );
	}
}
