<?php

namespace WooCustomFields;

defined( 'ABSPATH' ) || exit;

class OrderItemMods {

	/**
	 * Table name
	 */
	protected $table;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		global $wpdb;

		$this->table = $wpdb->prefix . 'woo_custom_fields';

		$this->hooks();

	}

	/**
	 * Register the hooks
	 *
	 * @since 0.1.0
	 */
	private function hooks() {

		add_action( 'woocommerce_new_order_item', array( $this, 'new_order_item' ), 10, 3 ); // add order item meta

		add_action( 'woocommerce_update_order_item', array( $this, 'update_order_item' ), 10, 3 ); // update order item meta

		add_action( 'woocommerce_delete_order_item', array( $this, 'delete_order_item' ), 10, 1 ); // delete order item meta

	}

	/**
	 * Get the data
	 *
	 * @since 0.1.0
	 */
	public function get_data( $item ) {

		if( $item->get_type() !== 'line_item' ) { // if order item is not a line item, stop
			return false;
		}

		$product = $item->get_product(); // returns correct product, based on variation_id for variables, or product_id for everything else

		if( ! is_object ( $product ) ) { // error handling if for any reason we don't have a product
			return;
		}

		$quantity = $item->get_quantity();
		$total    = $item->get_total();
		$cost     = $product->get_meta( '_product_cost' );

		if ( ! empty( $cost ) ) {

			$cost_total   = (float) $cost * (int) $quantity;
			$margin_total = (float) $total - $cost_total;

			$data = array(
				'cost'   => wc_format_decimal( $cost_total ),
				'margin' => wc_format_decimal( $margin_total ),
			);

			return $data;

		}

		return false;

	}

	/**
	 * Add Cost
	 * hooked into 'woocommerce_new_order_item'
	 * called by $order->add_product()
	 *
	 * @since 0.1.0
	 */
	public function new_order_item( $item_id, $item, $order_id ) {

		global $wpdb;

		$data = $this->get_data( $item );

		if ( $data ) {

			$args = array(
				'id'            => '',
				'order_item_id' => intval( $item_id ),
			);

			$data = array_merge( $args, $data );

			$wpdb->insert( $this->table, $data );

		}

	}

	/**
	 * Update Cost
	 * hooked into 'woocommerce_update_order_item'
	 * called by $item->update()
	 *
	 * @since 0.1.0
	 */
	public function update_order_item( $item_id, $item, $order_id ) {

		global $wpdb;

		$data = $this->get_data( $item );

		if ( $data ) {

			$where = array(
				'order_item_id' => intval( $item_id ),
			);

			$wpdb->update( $this->table, $data, $where );

		}

	}

	/**
	 * Delete Cost
	 * hooked into 'woocommerce_delete_order_item'
	 * called by $item->delete()
	 *
	 * @since 0.1.0
	 */
	public function delete_order_item( $item_id ) {

		global $wpdb;

		$where = array(
			'order_item_id' => intval( $item_id ),
		);

		$wpdb->delete( $this->table, $where );

	}


}
