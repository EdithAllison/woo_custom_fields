<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

delete_option( 'woo_custom_fields_db_version' );

$wpdb->query( "DROP TABLE IF EXISTS "  . $wpdb->prefix . 'woo_custom_fields');

