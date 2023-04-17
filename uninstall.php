<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function woo_custom_fields_uninstall_plugin() {

	global $wpdb;

	delete_option( 'woo_custom_fields_db_version' );

	$wpdb->query( "DROP TABLE IF EXISTS "  . $wpdb->prefix . 'woo_custom_fields');

}

// If we are in multisite we delete for all blogs (deletion = network level)
if ( is_multisite() ) {

	global $wpdb;

	// Get all blogs in the network and delete tables on each one
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	foreach ( $blog_ids as $blog_id ) {

		switch_to_blog( $blog_id );

		woo_custom_fields_uninstall_plugin();

		restore_current_blog();

	}

} else {

	woo_custom_fields_uninstall_plugin();

}
