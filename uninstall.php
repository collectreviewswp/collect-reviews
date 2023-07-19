<?php
/**
 * Remove all plugin data.
 *
 * @since 1.0.0
 */

use CollectReviews\ReviewRequests\Migrations\ReviewRequestsLimitLogsTable;
use CollectReviews\ReviewRequests\Migrations\ReviewRequestsMetaTable;
use CollectReviews\ReviewRequests\Migrations\ReviewRequestsTable;

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once 'vendor/autoload.php';

global $wpdb;

// Delete plugin settings.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'collect\_reviews%'" );

// Delete transients.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_collect\_reviews\_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_timeout\_collect\_reviews\_%'" );

// Delete all custom DB tables.
$db_tables = [
	ReviewRequestsTable::get_table_name(),
	ReviewRequestsMetaTable::get_table_name(),
	ReviewRequestsLimitLogsTable::get_table_name(),
];

foreach ( $db_tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS $table;" );
}

// Remove queue cron job.
wp_clear_scheduled_hook( 'collect_reviews_review_requests_queue' );
