<?php

namespace WooCustomFields;

defined( 'ABSPATH' ) || exit;

class Activator {

	/**
	 * Option DB Table version
	 */
	public static $db_version = '1.0.0';

	/**
	 * Activate
	 *
	 * @since    1.0.0
	 */
	public static function activate( $network_wide ) {

		global $wpdb;

		if ( is_multisite() &&  $network_wide ) {

			// Get all blogs in the network and activate plugin on each one
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			foreach ( $blog_ids as $blog_id ) {

				switch_to_blog( $blog_id );

				self::create_table();

				restore_current_blog();

			}

		} else {

			self::create_table();

		}

	}

	/**
	 * Create Database Table
	 */
	private static function create_table() {

		global $wpdb;

		$installed_ver = get_option( "woo_custom_fields_db_version" );

		if ( empty ($installed_ver) || $installed_ver != self::$db_version ) {

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$charset_collate = $wpdb->get_charset_collate();

			$table_name = $wpdb->prefix . 'woo_custom_fields';

			$sql = "CREATE TABLE $table_name (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				order_item_id bigint(20) NOT NULL,
				cost decimal(14,4),
				margin decimal(14,4),
				PRIMARY KEY  (id),
				KEY order_item_id (order_item_id)
			) $charset_collate;";

			dbDelta( $sql );

			update_option( "woo_custom_fields_db_version", self::$db_version);

		}

	}	// END CREATE_TABLE()


	/**
	 * Creating table whenever a new blog is created
	 * Run when a new blog is added to multisite
	 */
	public static function new_blog_table( $params) {

		if ( is_plugin_active_for_network( 'woo-custom-fieldds.php' ) ) {

			switch_to_blog( $params->blog_id );

			self::create_table();

			restore_current_blog();

		}

	}

}
