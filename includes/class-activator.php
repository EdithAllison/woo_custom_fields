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
	public static function activate() {

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

	}

}
